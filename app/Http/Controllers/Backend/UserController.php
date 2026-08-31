<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Contracts\UserRepositoryInterface;

use Illuminate\View\View;
class UserController extends Controller
{
    /**
     * UserController constructor.
     *
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(
        protected UserRepositoryInterface $userRepository
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

}
