<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\Auth\RegisterRequest;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterController extends Controller
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
    public function index(): View
    {
        return view('backend.auth.register', [
            'title' => 'Register',
            'bodyClassName' => 'register-page'
        ]);
    }

    /**
     * @param RegisterRequest $request
     *
     * @return RedirectResponse
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = $this->userRepository->create(
            $request->validated()
        );

        $user->assignRole('user');

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}
