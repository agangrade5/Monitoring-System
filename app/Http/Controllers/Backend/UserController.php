<?php
namespace App\Http\Controllers\Backend;
use App\Helpers\UtilityHelper;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\View\View;
use App\Http\Requests\Backend\User\ProfileUpdateRequest;
use App\Http\Requests\Backend\Auth\ChangePasswordRequest;
use Illuminate\Http\{RedirectResponse, JsonResponse};
use App\Http\Requests\Backend\User\UserRequest;
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
     * Show user profile
     * 
     * @return View
     * 
     * This method returns the view for the user profile page.
     * 
     */
    public function profile(): View
    {
        return view('backend.user.settings', [
            'title' => 'Settings',
            'bodyClassName' => 'Settings',
            'Settings' => ''
        ]);
    }

     /**
      * Store a new user.
      *
      * @param UserRequest $request
      *
      * @return \Illuminate\Http\RedirectResponse
      */
  public function storeUser(UserRequest $request)
    {
        $validated = $request->validated();
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
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateUser(UserRequest $request, int $id)
    {
    
        $validated = $request->validated();
        $this->userRepository->update($id, $validated);
        return redirect()
            ->back()
            ->with('success', 'User updated successfully.');
    }

     /**
     * Destroy an existing user.
     *
     * @param int $id
     * 
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
        $user = $request->user();

        $this->userRepository->updateProfile(
            $user,
            $request->validated()
        );

        /*
        |--------------------------------------------------------------------------
        | Activity Log
        |--------------------------------------------------------------------------
        */
        UtilityHelper::customActivityLog(
            'user',
            'Profile updated successfully.',
            $user,
            [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]
        );

        return back()->with(
            'success',
            'Profile updated successfully.'
        );
    }

    /**
     * Change authenticated user's password.
     *
     * @param ChangePasswordRequest $request
     *
     * @return JsonResponse
     */
    public function changePassword(
        ChangePasswordRequest $request
    ): JsonResponse {
        $user = $request->user();

        $this->userRepository->updatePassword(
            $user,
            $request->validated('password')
        );

        /*
        |--------------------------------------------------------------------------
        | Activity Log
        |--------------------------------------------------------------------------
        */
        UtilityHelper::customActivityLog(
            'auth',
            'Password changed successfully.',
            $user,
            [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Password updated successfully.',
        ]);
    }
}
