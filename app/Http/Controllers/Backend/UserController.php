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
