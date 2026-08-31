<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Contracts\UserRepositoryInterface;

use Illuminate\View\View;
class UserController extends Controller
{
     /**
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {
    }

    /**
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

    /**
     * Store a newly created user.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8 |confirmed',
        ]);

        $user = $this->userRepository->create($validated);
        
        // Assign default 'user' role
        $user->assignRole('user');

        return redirect()
            ->back()
            ->with('success', 'User created successfully.');
    }

}