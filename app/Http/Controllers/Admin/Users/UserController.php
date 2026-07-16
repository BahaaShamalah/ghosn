<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Users\StoreUserRequest;
use App\Http\Requests\Admin\Users\UpdateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $users = User::query()
            ->with('role')
            ->when($search !== '', function ($query) use ($search): void {
                $term = '%'.$search.'%';
                $query->where(function ($inner) use ($term): void {
                    $inner->where('name', 'like', $term)
                        ->orWhere('email', 'like', $term);
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create', $this->formData());
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        User::query()->create($request->validated());

        return redirect()
            ->route('admin.users.index')
            ->with('status', __('admin.users.created'));
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', array_merge($this->formData(), [
            'user' => $user->load('role'),
        ]));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return back()->with('status', __('admin.users.updated'));
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->is(auth()->user())) {
            return back()->withErrors(['user' => __('admin.users.cannot_delete_self')]);
        }

        if ($user->isSuperAdmin() && User::query()->where('role_id', $user->role_id)->count() <= 1) {
            return back()->withErrors(['user' => __('admin.users.cannot_delete_last_super_admin')]);
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', __('admin.users.deleted'));
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        return [
            'roles' => Role::query()->orderBy('label_en')->get(),
        ];
    }
}
