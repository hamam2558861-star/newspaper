<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index()
    {
        return view('admin.roles.index', ['roles' => Role::withCount('users')->orderBy('id')->get()]);
    }

    public function create()
    {
        return view('admin.roles.form', ['role' => new Role(['permissions' => []])]);
    }

    public function store(Request $request)
    {
        $data = $this->data($request);
        $data['name'] = 'custom_' . Str::lower(Str::random(6));
        $role = Role::create($data);
        ActivityLog::record('create', "إضافة دور: {$role->title}", $role);

        return redirect()->route('admin.roles.index')->with('success', 'تمت إضافة الدور.');
    }

    public function edit(Role $role)
    {
        abort_if($role->name === 'super_admin', 403, 'دور مدير النظام يملك كل الصلاحيات ولا يمكن تعديله.');

        return view('admin.roles.form', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        abort_if($role->name === 'super_admin', 403);
        $role->update($this->data($request));
        ActivityLog::record('update', "تعديل صلاحيات الدور: {$role->title}", $role);

        return redirect()->route('admin.roles.index')->with('success', 'تم تحديث الدور وصلاحياته.');
    }

    public function destroy(Role $role)
    {
        if ($role->is_system || $role->users()->exists()) {
            return back()->with('error', 'لا يمكن حذف دور نظامي أو دور مرتبط بمستخدمين.');
        }
        $role->delete();

        return back()->with('success', 'تم حذف الدور.');
    }

    private function data(Request $request): array
    {
        $all = collect(config('newsroom.permissions'))->flatMap(fn ($g) => array_keys($g))->all();

        $data = $request->validate([
            'title' => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:255'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => [Rule::in($all)],
        ], [], ['title' => 'اسم الدور']);

        $data['permissions'] = array_values($data['permissions'] ?? []);

        return $data;
    }
}
