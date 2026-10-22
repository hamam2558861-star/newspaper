<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('role')->withCount('articles')
            ->when($request->query('q'), fn ($q, $s) => $q->where(fn ($w) => $w->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%")))
            ->when($request->query('role'), fn ($q, $r) => $q->where('role_id', $r))
            ->latest()->paginate(20)->withQueryString();

        return view('admin.users.index', ['users' => $users, 'roles' => Role::orderBy('id')->get()]);
    }

    public function create()
    {
        return view('admin.users.form', ['user' => new User(['is_active' => true]), 'roles' => $this->assignableRoles()]);
    }

    public function store(Request $request)
    {
        $data = $this->data($request, null, true);
        $user = User::create($data);
        ActivityLog::record('create', "إضافة مستخدم: {$user->name}", $user);

        return redirect()->route('admin.users.index')->with('success', 'تمت إضافة المستخدم.');
    }

    public function edit(User $user)
    {
        $this->guardSuperAdmin($user);

        return view('admin.users.form', ['user' => $user, 'roles' => $this->assignableRoles()]);
    }

    public function update(Request $request, User $user)
    {
        $this->guardSuperAdmin($user);
        $data = $this->data($request, $user);
        if ($user->id === $request->user()->id) {
            unset($data['is_active']);
        }
        $user->update($data);
        ActivityLog::record('update', "تعديل مستخدم: {$user->name}", $user);

        return redirect()->route('admin.users.index')->with('success', 'تم تحديث المستخدم.');
    }

    public function destroy(Request $request, User $user)
    {
        $this->guardSuperAdmin($user);
        if ($user->id === $request->user()->id) {
            return back()->with('error', 'لا يمكنك حذف حسابك الحالي.');
        }
        if ($user->articles()->withTrashed()->exists()) {
            return back()->with('error', 'لدى هذا المستخدم مواد منشورة؛ يرجى تعطيل الحساب بدلاً من حذفه.');
        }
        ActivityLog::record('delete', "حذف مستخدم: {$user->name}", $user);
        delete_upload($user->avatar);
        $user->delete();

        return back()->with('success', 'تم حذف المستخدم.');
    }

    /** غير مدير النظام لا يستطيع تعديل مدراء النظام أو منح دورهم. */
    private function assignableRoles()
    {
        return Role::when(! auth()->user()->isSuperAdmin(), fn ($q) => $q->where('name', '!=', 'super_admin'))->orderBy('id')->get();
    }

    private function guardSuperAdmin(User $user): void
    {
        abort_if($user->isSuperAdmin() && ! auth()->user()->isSuperAdmin(), 403);
    }

    private function data(Request $request, ?User $user = null, bool $creating = false): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user?->id)],
            'role_id' => ['required', 'exists:roles,id'],
            'job_title' => ['nullable', 'string', 'max:120'],
            'bio' => ['nullable', 'string', 'max:600'],
            'phone' => ['nullable', 'string', 'max:30'],
            'twitter' => ['nullable', 'string', 'max:60'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'password' => [$creating ? 'required' : 'nullable', Password::min(8)],
        ], [], ['name' => 'الاسم', 'email' => 'البريد', 'role_id' => 'الدور الوظيفي', 'password' => 'كلمة المرور']);

        abort_if(
            ! auth()->user()->isSuperAdmin() && Role::find($data['role_id'])?->name === 'super_admin',
            403
        );

        if ($request->hasFile('avatar')) {
            delete_upload($user?->avatar);
            $data['avatar'] = store_upload($request->file('avatar'), 'avatars');
        }
        if (empty($data['password'])) {
            unset($data['password']);
        }
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
