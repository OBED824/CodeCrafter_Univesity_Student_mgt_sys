<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminRoleController extends Controller
{
    public function index(): View
    {
        $roles = Role::query()
            ->whereIn('name', ['lecturer', 'student'])
            ->with('permissions')
            ->orderBy('name')
            ->get();

        return view('admin.roles.index', [
            'roles' => $roles,
            'permissionsByRole' => $roles->mapWithKeys(fn (Role $role) => [
                $role->name => Permission::query()
                    ->whereIn('name', $this->permissionsFor($role->name))
                    ->orderBy('name')
                    ->get(),
            ]),
        ]);
    }

    public function update(Request $request, string $role): RedirectResponse
    {
        abort_unless(in_array($role, ['lecturer', 'student'], true), 404);
        $allowedPermissionIds = Permission::query()
            ->whereIn('name', $this->permissionsFor($role))
            ->pluck('id')
            ->all();

        $validated = $request->validate([
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['integer', 'distinct', Rule::in($allowedPermissionIds)],
        ]);

        $roleModel = Role::findByName($role, 'web');
        $permissions = Permission::query()
            ->whereIn('id', $validated['permissions'] ?? [])
            ->get();

        $roleModel->syncPermissions($permissions);

        return redirect()->route('admin.roles.index')
            ->with('status', ucfirst($role).' permissions updated.');
    }

    /**
     * @return list<string>
     */
    private function permissionsFor(string ...$roles): array
    {
        $permissions = [
            'lecturer' => [
                'view own courses',
                'view course students',
                'view assignments',
                'manage assignments',
                'view course grades',
                'manage grades',
                'view course notes',
                'manage course notes',
            ],
            'student' => [
                'register courses',
                'view own registrations',
                'view assignments',
                'view own grades',
                'view course notes',
                'view own attendance',
                'record attendance',
            ],
        ];

        return array_values(array_unique(array_merge(...array_map(
            fn (string $role): array => $permissions[$role] ?? [],
            $roles,
        ))));
    }
}
