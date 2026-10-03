<#
.SYNOPSIS
    Sets up the TECH COMPONENTS dashboard on a Windows machine.

.DESCRIPTION
    Installs the toolchain (PHP 8.3+, Composer, Node.js LTS), restores the
    Composer and npm dependencies, creates the MySQL database, runs the
    migrations and catalog seeder, builds the front-end assets and links the
    storage directory. Safe to re-run: every step detects what is already in
    place and skips it.

.PARAMETER DbHost
    MySQL host. Defaults to 127.0.0.1 (XAMPP).

.PARAMETER DbPort
    MySQL port. Defaults to 3306.

.PARAMETER DbName
    Database to create and use. Defaults to tech_components.

.PARAMETER DbUser
    MySQL user. Defaults to xampp.

.PARAMETER DbPassword
    MySQL password. Defaults to xampp. Written into .env, so prefer the prompt
    or -SkipPrompts over passing this on a shared command line.

.PARAMETER SkipPrompts
    Accept every default instead of prompting. Use this for unattended runs.

.PARAMETER SkipToolchainInstall
    Never install anything system-wide. Missing tools become an error with
    instructions instead of an automatic winget install.

.PARAMETER SkipServe
    Do not start "php artisan serve" when finished.

.EXAMPLE
    .\setup.ps1

.EXAMPLE
    .\setup.ps1 -DbUser root -DbPassword secret -SkipPrompts
#>

#Requires -Version 5.1

[CmdletBinding()]
param(
    [string] $DbHost = '127.0.0.1',
    [int]    $DbPort = 3306,
    [string] $DbName = 'tech_components',
    [string] $DbUser = 'xampp',
    [string] $DbPassword = 'xampp',
    [switch] $SkipPrompts,
    [switch] $SkipToolchainInstall,
    [switch] $SkipServe
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'
$ProgressPreference = 'SilentlyContinue'

$script:Root = if ($PSScriptRoot) { $PSScriptRoot } else { (Get-Location).Path }

# $IsWindows only exists on PowerShell 6+, and StrictMode turns reading an
# undefined variable into a terminating error, so ask for it safely instead.
$script:OnWindows = $env:OS -eq 'Windows_NT'

if (-not $script:OnWindows -and (Get-Variable -Name 'IsWindows' -ErrorAction SilentlyContinue)) {
    $script:OnWindows = [bool] $IsWindows
}

# Extensions Laravel, Composer and the MySQL connection all need. php.ini ships
# with most of these already enabled, but pdo_mysql and zip are commented out in
# php.ini-development and have to be switched on explicitly.
$RequiredPhpExtensions = @(
    'ctype', 'dom', 'fileinfo', 'filter', 'hash', 'json', 'libxml',
    'mbstring', 'openssl', 'pcre', 'phar', 'session', 'tokenizer',
    'xml', 'xmlwriter', 'zip', 'curl', 'pdo_mysql'
)

# --------------------------------------------------------------- output --

function Write-Step {
    param([string] $Message)
    Write-Host ''
    Write-Host "==> $Message" -ForegroundColor Cyan
}

function Write-Detail {
    param([string] $Message)
    Write-Host "    $Message" -ForegroundColor DarkGray
}

function Write-Ok {
    param([string] $Message)
    Write-Host "    [ok] $Message" -ForegroundColor Green
}

function Write-Warn {
    param([string] $Message)
    Write-Host "    [!!] $Message" -ForegroundColor Yellow
}

function Write-Fail {
    param([string] $Message)
    Write-Host "    [xx] $Message" -ForegroundColor Red
}

function Write-Question {
    param([string] $Message)
    Write-Host "    [??] $Message" -ForegroundColor Magenta
}

# ------------------------------------------------------------- utilities --

function Test-IsAdministrator {
    if (-not $script:OnWindows) { return $false }

    try {
        $identity = [Security.Principal.WindowsIdentity]::GetCurrent()
        $principal = New-Object Security.Principal.WindowsPrincipal($identity)

        return $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
    } catch {
        return $false
    }
}

function Test-HasWinget {
    return $null -ne (Get-Command 'winget.exe' -ErrorAction SilentlyContinue)
}

function Update-SessionPath {
    # Tools installed mid-session are not on the current PATH because Windows
    # only rebuilds it for new processes. Re-read it from the registry.
    $machine = [Environment]::GetEnvironmentVariable('Path', 'Machine')
    $user = [Environment]::GetEnvironmentVariable('Path', 'User')

    if ($machine) { $env:Path = "$machine;$env:Path" }
    if ($user) { $env:Path = "$user;$env:Path" }
}

function Invoke-External {
    <#
        Runs a native command and throws when it reports a non-zero exit code.
        Output is deliberately streamed so long-running steps such as
        "composer install" show progress instead of looking hung.
    #>
    param(
        [Parameter(Mandatory)] [string] $FilePath,
        [string[]] $ArgumentList = @(),
        [string] $What
    )

    $label = if ($What) { $What } else { "$FilePath $($ArgumentList -join ' ')" }
    Write-Detail "$label ..."

    & $FilePath @ArgumentList

    if ($LASTEXITCODE -ne 0) {
        throw "$label failed with exit code $LASTEXITCODE."
    }
}

function Invoke-WingetInstall {
    param([Parameter(Mandatory)] [string] $Id)

    Invoke-External -FilePath 'winget.exe' -ArgumentList @(
        'install', '--id', $Id, '-e', '--silent',
        '--accept-package-agreements', '--accept-source-agreements',
        '--disable-interactivity'
    ) -What "winget install $Id"
}

function Test-Internet {
    try {
        Invoke-WebRequest -Uri 'https://windows.php.net' -Method Head -TimeoutSec 15 -UseBasicParsing | Out-Null
        return $true
    } catch {
        return $false
    }
}

function Assert-Elevated {
    if (Test-IsAdministrator) { return }

    throw 'This needs Administrator rights to install the missing toolchain. Right-click Windows PowerShell, choose "Run as administrator", and run this script again.'
}

function Read-PhpVersion {
    $raw = & php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;' 2>$null

    if (-not $raw) { return $null }

    return [Version] "$raw".Trim()
}

function Test-PhpVersion {
    param([Version] $Minimum = '8.3')

    $version = Read-PhpVersion

    if (-not $version) { return $false }

    return $version -ge $Minimum
}

function Read-NodeVersion {
    $raw = & node --version 2>$null

    if (-not $raw) { return $null }

    return [Version] "$raw".Trim().TrimStart('v')
}

function Test-NodeVersion {
    # [Version] needs at least major.minor, so a bare "20" would be rejected.
    param([Version] $Minimum = '20.0')

    $version = Read-NodeVersion

    if (-not $version) { return $false }

    return $version -ge $Minimum
}

function Get-NpmCommand {
    # PowerShell 5.1 resolves "npm" to npm.ps1, which trips over the default
    # execution policy. The .cmd shim always works.
    if ($script:OnWindows) { return 'npm.cmd' }

    return 'npm'
}

# ------------------------------------------------------------------ php --

function Initialize-Php {
    if (Test-PhpVersion) {
        Write-Ok "PHP $(Read-PhpVersion) already installed."
        return
    }

    $installed = Read-PhpVersion

    if ($installed) {
        Write-Warn "PHP $installed is older than the 8.3 this project needs."
    } else {
        Write-Detail 'PHP 8.3 or newer is required and was not found.'
    }

    if ($SkipToolchainInstall) {
        throw 'PHP 8.3+ is required. Install it from https://windows.php.net/download/ and re-run.'
    }

    Assert-Elevated

    # winget ships the official MSI, which lays down php.ini and the extension
    # directory for us. Try the newest release first.
    if (Test-HasWinget) {
        foreach ($package in @('PHP.PHP.8.4', 'PHP.PHP.8.3')) {
            Write-Detail "Installing $package via winget..."
            Invoke-WingetInstall -Id $package
            Update-SessionPath

            if (Test-PhpVersion) {
                Write-Ok "PHP $(Read-PhpVersion) installed."
                return
            }
        }
    }

    throw 'Could not install PHP automatically. Install PHP 8.3+ from https://windows.php.net/download/ and re-run this script.'
}

function Initialize-PhpExtensions {
    $missing = $RequiredPhpExtensions | Where-Object { (& php -m 2>$null) -notcontains $_ }

    if (-not $missing) {
        Write-Ok "All required extensions loaded ($($RequiredPhpExtensions.Count) checked)."
        return
    }

    $iniPath = Get-PhpIniPath

    if (-not $iniPath) {
        throw "php.ini was not found, so these extensions cannot be enabled: $($missing -join ', ')."
    }

    Write-Detail "Enabling in ${iniPath}: $($missing -join ', ')"

    $contents = Get-Content $iniPath -Raw

    foreach ($extension in $missing) {
        $escaped = [regex]::Escape($extension)
        $pattern = "(?im)^\s*[;#]?\s*extension\s*=\s*$escaped\s*$"

        if ($contents -match $pattern) {
            # Already present but commented out.
            $contents = [regex]::Replace($contents, $pattern, "extension=$extension")
        } else {
            $contents += "`r`n; enabled by setup.ps1`r`nextension=$extension"
        }
    }

    Set-Content -Path $iniPath -Value $contents -NoNewline

    $stillMissing = $RequiredPhpExtensions | Where-Object { (& php -m 2>$null) -notcontains $_ }

    if ($stillMissing) {
        throw "Could not enable these extensions: $($stillMissing -join ', '). Add the matching extension= lines to php.ini by hand."
    }

    Write-Ok "Extensions enabled: $($missing -join ', ')."
}

function Get-PhpIniPath {
    <#
        Returns the active php.ini, creating one from php.ini-development when
        PHP has been installed but never configured. Returns nothing when there
        is no ini file and no template to build one from.
    #>
    $iniOutput = & php --ini 2>$null

    $loaded = ($iniOutput | Where-Object { $_ -match '^Loaded Configuration File:' } | Select-Object -First 1) `
        -replace '^Loaded Configuration File:\s*', ''

    $loaded = "$loaded".Trim()

    if ($loaded -and $loaded -ne '(none)' -and (Test-Path $loaded)) {
        return $loaded
    }

    # A stock Windows PHP install has php.ini-development sitting next to the
    # binary but no active php.ini, so nothing beyond the built-ins is loaded.
    $searchDir = ($iniOutput | Where-Object { $_ -match '^Configuration File \(php\.ini\) Path:' } | Select-Object -First 1) `
        -replace '^Configuration File \(php\.ini\) Path:\s*', ''

    $searchDir = "$searchDir".Trim()

    if (-not $searchDir -or -not (Test-Path $searchDir)) { return $null }

    $iniPath = Join-Path $searchDir 'php.ini'

    if (Test-Path $iniPath) { return $iniPath }

    $template = Join-Path $searchDir 'php.ini-development'

    if (-not (Test-Path $template)) { return $null }

    Copy-Item $template $iniPath -Force
    Write-Detail 'Created php.ini from php.ini-development.'

    return $iniPath
}

# ------------------------------------------------------------- composer --

function Initialize-Composer {
    if (Get-Command 'composer' -ErrorAction SilentlyContinue) {
        Write-Ok 'Composer already installed.'
        return
    }

    if ($SkipToolchainInstall) {
        throw 'Composer is required. Install it from https://getcomposer.org/Composer-Setup.exe and re-run.'
    }

    Assert-Elevated

    if (Test-HasWinget) {
        Write-Detail 'Installing Composer.Composer via winget...'
        Invoke-WingetInstall -Id 'Composer.Composer'
        Update-SessionPath
    }

    if (-not (Get-Command 'composer' -ErrorAction SilentlyContinue)) {
        throw 'Could not install Composer automatically. Install it from https://getcomposer.org/Composer-Setup.exe and re-run this script.'
    }

    Write-Ok 'Composer installed.'
}

# ----------------------------------------------------------------- node --

function Initialize-Node {
    if (Test-NodeVersion) {
        Write-Ok "Node $(Read-NodeVersion) already installed."
        return
    }

    Write-Detail 'Node.js 20 or newer is required to build the front-end assets.'

    if ($SkipToolchainInstall) {
        throw 'Node.js 20+ is required. Install the LTS build from https://nodejs.org/ and re-run.'
    }

    Assert-Elevated

    if (Test-HasWinget) {
        Write-Detail 'Installing OpenJS.NodeJS.LTS via winget...'
        Invoke-WingetInstall -Id 'OpenJS.NodeJS.LTS'
        Update-SessionPath
    }

    if (-not (Test-NodeVersion)) {
        throw 'Could not install Node.js automatically. Install the LTS build from https://nodejs.org/ and re-run this script.'
    }

    Write-Ok "Node $(Read-NodeVersion) installed."
}

# ---------------------------------------------------------------- mysql --

function Find-MySqlClient {
    if ($env:DbClientPath -and (Test-Path $env:DbClientPath)) {
        return $env:DbClientPath
    }

    $onPath = Get-Command 'mysql.exe' -ErrorAction SilentlyContinue

    if ($onPath) { return $onPath.Source }

    $roots = @()

    foreach ($variable in @('ProgramFiles', 'ProgramW6432', 'ProgramFiles(x86)')) {
        $base = [Environment]::GetEnvironmentVariable($variable)

        if ($base) { $roots += $base }
    }

    # Official MySQL Server installs, then XAMPP's bundled client.
    foreach ($root in $roots) {
        foreach ($folder in @('MySQL', 'xampp\mysql\bin', 'php', 'laragon\bin\mysql')) {
            $directory = Join-Path $root $folder

            if (-not (Test-Path $directory)) { continue }

            $found = Get-ChildItem -Path $directory -Filter 'mysql.exe' -Recurse -ErrorAction SilentlyContinue |
                Select-Object -First 1

            if ($found) { return $found.FullName }
        }
    }

    return $null
}

function Test-MySqlPort {
    $client = New-Object Net.Sockets.TcpClient

    try {
        $connect = $client.ConnectAsync($DbHost, $DbPort)

        return $connect.Wait(3000) -and $client.Connected
    } catch {
        return $false
    } finally {
        $client.Dispose()
    }
}

function Initialize-MySqlServer {
    if (Test-MySqlPort) {
        Write-Ok "MySQL is accepting connections on ${DbHost}:$DbPort."
        return
    }

    Write-Warn "Nothing is listening on ${DbHost}:$DbPort."

    $service = Get-Service -Name 'MySQL*' -ErrorAction SilentlyContinue | Select-Object -First 1

    if ($service -and $service.Status -ne 'Running') {
        Write-Detail "Starting the '$($service.Name)' service..."
        Start-Service -Name $service.Name -ErrorAction Stop
        Start-Sleep -Seconds 3
    }

    if (Test-MySqlPort) {
        Write-Ok 'MySQL service started.'
        return
    }

    throw "Cannot reach MySQL on ${DbHost}:$DbPort. Start it from the XAMPP Control Panel (open MySQL, then Start) and re-run this script."
}

function Initialize-MySqlDatabase {
    $client = Find-MySqlClient

    if (-not $client) {
        Write-Warn 'mysql.exe was not found, so the database cannot be created for you.'
        Write-Detail "Create it by hand, then re-run: CREATE DATABASE ``$DbName`` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
        return
    }

    Write-Detail "Using $client"

    # The password is passed inline because mysql.exe has no prompt-free flag
    # that is safe to automate. Whatever is typed stays on this machine.
    $arguments = @(
        "-h$DbHost", "-P$DbPort", "-u$DbUser",
        '--default-character-set=utf8mb4',
        '-e', "CREATE DATABASE IF NOT EXISTS ``$DbName`` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    )

    if ($DbPassword) { $arguments += "-p$DbPassword" }

    & $client @arguments

    if ($LASTEXITCODE -ne 0) {
        throw "Could not create the database '$DbName'. Check the MySQL username and password."
    }

    Write-Ok "Database '$DbName' is ready."
}

# ------------------------------------------------------------ .env file --

function ConvertTo-DotEnvValue {
    # dotenv treats a value as unquoted text, so anything containing whitespace
    # or a comment marker has to be quoted or the line stops parsing there.
    param([string] $Value)

    if ($Value -match '[\s#"]') {
        # Verified against phpdotenv: it unescapes \" inside a double-quoted
        # value, and rejects a bare doubled quote outright.
        return '"' + $Value.Replace('\', '\\').Replace('"', '\"') + '"'
    }

    return $Value
}

function ConvertTo-Crlf {
    # Git checkouts on Windows mix LF and CRLF, and the lines appended below use
    # CRLF. Normalising keeps the rewritten .env from ending up half-and-half.
    param([string] $Content)

    return ($Content -replace "`r`n", "`n") -replace "`n", "`r`n"
}

function Initialize-EnvFile {
    $envPath = Join-Path $script:Root '.env'
    $examplePath = Join-Path $script:Root '.env.example'

    if (Test-Path $envPath) {
        Write-Ok '.env already exists, keeping it.'
    } else {
        if (-not (Test-Path $examplePath)) {
            throw '.env.example is missing, so .env cannot be created.'
        }

        Copy-Item $examplePath $envPath -Force
        Write-Ok '.env created from .env.example.'
    }

    $content = Get-Content $envPath -Raw

    $updates = [ordered]@{
        'DB_CONNECTION' = 'mysql'
        'DB_HOST'       = $DbHost
        'DB_PORT'       = "$DbPort"
        'DB_DATABASE'   = $DbName
        'DB_USERNAME'   = $DbUser
        'DB_PASSWORD'   = $DbPassword
    }

    foreach ($key in $updates.Keys) {
        $line = "$key=$(ConvertTo-DotEnvValue $updates[$key])"

        if ($content -match "(?m)^$([regex]::Escape($key))=") {
            # A MatchEvaluator keeps a password containing $ or \ from being
            # read as a regex substitution.
            $evaluator = [System.Text.RegularExpressions.MatchEvaluator] { param($match) $line }
            $content = [regex]::Replace($content, "(?m)^$([regex]::Escape($key))=.*$", $evaluator)
        } else {
            $content += "`r`n$line"
        }
    }

    Set-Content -Path $envPath -Value (ConvertTo-Crlf $content) -NoNewline
    Write-Ok ".env points at mysql://$DbUser@${DbHost}:$DbPort/$DbName"
}

# ---------------------------------------------------------- db questions --

function Request-DatabaseSetting {
    if ($SkipPrompts) {
        Write-Detail 'Using the supplied database settings.'
        return
    }

    Write-Question 'XAMPP defaults are preselected. Press Enter to keep each one.'

    # Every write goes through Set-Variable: a plain assignment inside a
    # function would only change that function's local copy and leave the rest
    # of the script on the value it was given.
    $fields = @(
        @{ Label = 'Database host'; Name = 'DbHost'; Current = $DbHost }
        @{ Label = 'Database name'; Name = 'DbName'; Current = $DbName }
        @{ Label = 'MySQL user'; Name = 'DbUser'; Current = $DbUser }
    )

    foreach ($field in $fields) {
        $typed = Read-Host "    $($field.Label) [$($field.Current)]"

        if ($typed) {
            Set-Variable -Name $field.Name -Value $typed -Scope Script
            Write-Detail "$($field.Label) = $typed"
        }
    }

    $typedPort = Read-Host '    Database port [3306]'
    $parsedPort = 0

    if ($typedPort -and [int]::TryParse($typedPort, [ref] $parsedPort)) {
        Set-Variable -Name 'DbPort' -Value $parsedPort -Scope Script
        Write-Detail "Database port = $parsedPort"
    } elseif ($typedPort) {
        Write-Warn "'$typedPort' is not a valid port. Keeping $DbPort."
    }

    # Read-Host echoes what is typed unless told otherwise, and a database
    # password should not sit in the console scrollback.
    $secure = Read-Host '    MySQL password [xampp]' -AsSecureString
    $plain = [Net.NetworkCredential]::new('', $secure).Password

    if ($plain) {
        Set-Variable -Name 'DbPassword' -Value $plain -Scope Script
        Write-Detail 'MySQL password = (hidden)'
    }
}

# ------------------------------------------------------------------ main --

function Invoke-Main {
    Write-Host ''
    Write-Host 'TECH COMPONENTS - Windows setup' -ForegroundColor White
    Write-Host "Project folder: $script:Root" -ForegroundColor DarkGray

    Push-Location $script:Root

    try {
        # -- preflight --
        Write-Step 'Checking the environment'

        if (-not $script:OnWindows) {
            throw 'setup.ps1 only runs on Windows. Use the manual steps in README.md instead.'
        }

        if (Test-IsAdministrator) {
            Write-Ok 'Running with Administrator rights.'
        } elseif ($SkipToolchainInstall) {
            Write-Warn 'Not running as Administrator. Missing tools will not be installed.'
        } else {
            Write-Warn 'Not running as Administrator. A prompt will appear if a tool has to be installed.'
        }

        $hasComposer = $null -ne (Get-Command 'composer' -ErrorAction SilentlyContinue)
        $needsNetwork = -not (Test-PhpVersion) -or -not $hasComposer -or -not (Test-NodeVersion)

        if ($needsNetwork -and -not $SkipToolchainInstall -and -not (Test-Internet)) {
            throw 'No internet connection. PHP, Composer and Node.js are downloaded during setup.'
        }

        # -- toolchain --
        Write-Step 'Preparing the toolchain'
        Initialize-Php
        Initialize-PhpExtensions
        Initialize-Composer
        Initialize-Node

        # -- dependencies --
        Write-Step 'Installing PHP dependencies'
        Invoke-External -FilePath 'composer' -ArgumentList @('install', '--no-interaction', '--no-progress') -What 'composer install'

        # -- environment --
        Write-Step 'Configuring the environment'
        Initialize-EnvFile
        Invoke-External -FilePath 'php' -ArgumentList @('artisan', 'key:generate', '--force') -What 'php artisan key:generate'

        # -- database --
        Write-Step 'Preparing the MySQL database'
        Request-DatabaseSetting
        Initialize-MySqlServer
        Initialize-MySqlDatabase
        Invoke-External -FilePath 'php' -ArgumentList @('artisan', 'migrate', '--force') -What 'php artisan migrate'
        Invoke-External -FilePath 'php' -ArgumentList @('artisan', 'db:seed', '--force') -What 'php artisan db:seed (the 25 catalog products)'

        # -- front end --
        Write-Step 'Building the front-end assets'
        $npm = Get-NpmCommand
        Invoke-External -FilePath $npm -ArgumentList @('install', '--no-audit', '--no-fund') -What "$npm install"
        Invoke-External -FilePath $npm -ArgumentList @('run', 'build') -What "$npm run build"

        # -- storage --
        Write-Step 'Linking storage and clearing caches'
        Invoke-External -FilePath 'php' -ArgumentList @('artisan', 'storage:link') -What 'php artisan storage:link'
        Invoke-External -FilePath 'php' -ArgumentList @('artisan', 'optimize:clear') -What 'php artisan optimize:clear'

        # -- done --
        Write-Host ''
        Write-Host 'Setup finished.' -ForegroundColor Green
        Write-Host ''
        Write-Host '    Start the dashboard with:  php artisan serve' -ForegroundColor White
        Write-Host '    Then open:                http://127.0.0.1:8000' -ForegroundColor White
        Write-Host ''
        Write-Host '    Restore the original 25 products with:  php artisan catalog:reset' -ForegroundColor DarkGray
        Write-Host ''

        if ($SkipServe) { return }

        $answer = Read-Host 'Start "php artisan serve" now? [Y/n]'

        if ($answer -and $answer -notmatch '^[Yy]') { return }

        Write-Step 'Starting php artisan serve (press Ctrl+C to stop)'
        & php artisan serve
    } finally {
        Pop-Location
    }
}

try {
    Invoke-Main
} catch {
    Write-Host ''
    Write-Fail $_.Exception.Message
    Write-Host ''

    if (-not (Test-IsAdministrator) -and -not $SkipToolchainInstall) {
        Write-Host 'Tip: right-click Windows PowerShell and choose "Run as administrator",' -ForegroundColor Yellow
        Write-Host '     then run this script again so it can install the missing tools.' -ForegroundColor Yellow
        Write-Host ''
    }

    exit 1
}

exit 0