@echo off
REM Double-click launcher for setup.ps1.
REM
REM Run this if you would rather not right-click and "Run with PowerShell".
REM The -ExecutionPolicy Bypass flag only applies to this one process, so it
REM does not weaken the machine-wide policy. Any arguments are passed straight
REM through, so setup.cmd -SkipServe behaves the same as the .ps1.

setlocal
cd /d "%~dp0"

powershell.exe -NoProfile -ExecutionPolicy Bypass -File "%~dp0setup.ps1" %*
set "EXITCODE=%ERRORLEVEL%"

if not "%EXITCODE%"=="0" (
    echo.
    echo Setup stopped with exit code %EXITCODE%. Scroll up for the reason.
    pause
)

endlocal & exit /b %EXITCODE%