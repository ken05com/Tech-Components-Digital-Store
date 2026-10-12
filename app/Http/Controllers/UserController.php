<?php

namespace App\Http\Controllers;

use App\Auth\Role;
use App\Auth\User;
use App\Auth\UserRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function __construct(private readonly UserRepository $users) {}

    public function index(): View
    {
        return view('users.index', [
            'users' => $this->users->all(),
            'roleCounts' => collect(Role::cases())->mapWithKeys(
                fn (Role $role) => [$role->value => $this->users->countByRole($role)]
            ),
            'totalUsers' => $this->users->count(),
            'pageTitle' => 'Users & Roles',
        ]);
    }

    public function create(): View
    {
        return view('users.form', [
            'user' => null,
            'roles' => Role::cases(),
            'pageTitle' => 'Add User',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:160', Rule::unique('users', 'email')],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => ['required', Rule::in(Role::values())],
        ]);

        $data['role'] = Role::from($data['role']);

        $this->users->create($data);

        return redirect()
            ->route('users.index')
            ->with('success_dialog', 'User created');
    }

    public function edit(int $user): View
    {
        $record = $this->users->find($user);

        if ($record === null) {
            abort(404, 'User not found.');
        }

        return view('users.form', [
            'user' => $record,
            'roles' => Role::cases(),
            'pageTitle' => 'Edit '.$record->name,
        ]);
    }

    public function update(Request $request, int $user): RedirectResponse
    {
        $record = $this->users->find($user);

        if ($record === null) {
            abort(404, 'User not found.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:160', Rule::unique('users', 'email')->ignore($record->id)],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'role' => ['required', Rule::in(Role::values())],
        ]);

        $role = Role::from($data['role']);

        if ($record->id === $request->user()?->id && $role !== Role::SuperAdmin) {
            // Do not let the acting Super Admin lock themselves out.
            if ($request->user()->is(Role::SuperAdmin)) {
                return back()->withErrors(['role' => 'You cannot change your own role away from Super Admin.'])->withInput();
            }
        }

        if ($this->isLastSuperAdmin($record) && $role !== Role::SuperAdmin) {
            return back()->withErrors(['role' => 'There must always be at least one Super Admin.'])->withInput();
        }

        $data['role'] = $role;
        $this->users->update($record->id, $data);

        return redirect()
            ->route('users.index')
            ->with('success_dialog', 'User updated');
    }

    public function destroy(Request $request, int $user): RedirectResponse
    {
        $record = $this->users->find($user);

        if ($record === null) {
            return redirect()->route('users.index')->with('status', 'That user no longer exists.');
        }

        if ($record->id === $request->user()?->id) {
            return redirect()->route('users.index')->withErrors(['user' => 'You cannot delete your own account.']);
        }

        if ($this->isLastSuperAdmin($record)) {
            return redirect()->route('users.index')->withErrors(['user' => 'The last Super Admin cannot be deleted.']);
        }

        $this->users->delete($record->id);

        return redirect()
            ->route('users.index')
            ->with('success_dialog', 'User deleted');
    }

    private function isLastSuperAdmin(User $user): bool
    {
        return $user->role === Role::SuperAdmin && $this->users->countByRole(Role::SuperAdmin) <= 1;
    }
}
