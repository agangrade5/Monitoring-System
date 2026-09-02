<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\View\View;
use App\Http\Requests\Backend\User\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param UserRepositoryInterface $userRepository
     *
     * @return void
     */
    public function __construct(
        protected UserRepositoryInterface $userRepository
    ) {
    }

    /**
     * List all users
     *
     * @return View
     */
    public function allUsers(): View
    {
        $search = request('search');

        $users = $this->userRepository->getAllUsers($search);

        return view('backend.admin.user', [
            'title' => 'User',
            'bodyClassName' => 'user-page',
            'users' => $users,
        ]);
    }
    public function profile(): View
{
    return view('backend.user.settings', [
        'title' => 'Settings',
        'bodyClassName' => 'Settings',
        'Settings' => ''
    ]);
}

  public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'is_active' => 'required|boolean',
        ]);

        $user = $this->userRepository->create($validated);
        
        // Assign default 'user' role
        $user->assignRole('user');

        return redirect()
            ->back()
            ->with('success', 'User created successfully.');
    }
/**
     * Update an existing user.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateUser(Request $request, int $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            'is_active' => 'required|boolean',
        ]);

        $this->userRepository->update($id, $validated);

        return redirect()
            ->back()
            ->with('success', 'User updated successfully.');
    }

 /**
     * Destroy an existing user.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroyUser(int $id)
    {
        $this->userRepository->delete($id);

        return redirect()
            ->back()
            ->with('success', 'User deleted successfully.');
    }
    /**
     * Update user profile
     *
     * @param ProfileUpdateRequest $request
     *
     * @return RedirectResponse
     */
    public function updateProfile(
        ProfileUpdateRequest $request
    ): RedirectResponse {
        $this->userRepository->updateProfile(
            $request->user(),
            $request->validated()
        );
        return back()->with(
            'success',
            'Profile updated successfully.'
        );
    }
}
