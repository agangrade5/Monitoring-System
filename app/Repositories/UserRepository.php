<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Format;

class UserRepository implements UserRepositoryInterface
{
    /**
     * Method create
     *
     * @param array $data
     *
     * @return User
     */
    public function create(array $data): User
    {
        $data['password'] = Hash::make($data['password']);

        return User::create($data);
    }

    /**
     * Method updateUser
     *
     * @param string $email
     *
     * @return User
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Method to retrieve all users
     *
     * @return array<User>
     */
    public function getAllUsers(): LengthAwarePaginator
    {
        return User::latest()->paginate(10);
    }

    /**
     * Update user password.
     *
     * @param User $user
     * @param string $password
     *
     * @return bool
     */
    public function updatePassword(
        User $user,
        string $password
    ): bool {
        return $user->update([
            'password' => Hash::make($password),
        ]);
    }

    /**
     * Update user profile.
     *
     * @param User $user
     * @param array $data
     *
     * @return bool
     */
    public function updateProfile(
        User $user,
        array $data
    ): bool {
        $disk = config('filesystems.default');
        /*
        |--------------------------------------------------------------------------
        | Remove Profile Image
        |--------------------------------------------------------------------------
        */
        if (
            !empty($data['remove_profile_image']) &&
            $user->image
        ) {
            Storage::disk($disk)->delete(
                $user->image
            );

            $data['image'] = null;
        }

        /*
        |--------------------------------------------------------------------------
        | Upload New Profile Image
        |--------------------------------------------------------------------------
        */
        if (
            isset($data['profile_image']) &&
            $data['profile_image'] instanceof UploadedFile
        ) {
            /*
            |----------------------------------------------------------------------
            | Delete Old Image
            |----------------------------------------------------------------------
            */
            if ($user->image) {
                Storage::disk($disk)->delete($user->image);
            }

            $data['image'] = $this->uploadProfileImage(
                $user,
                $data['profile_image'],
                $disk
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Remove Form Only Field
        |--------------------------------------------------------------------------
        */
        unset(
            $data['remove_profile_image'],
            $data['profile_image']
        );

        return $user->update($data);
    }

    /**
     * Upload profile image.
     *
     * @param User $user
     * @param UploadedFile $file
     * @param string $disk
     *
     * @return string
     */
    private function uploadProfileImage(
        User $user,
        UploadedFile $file,
        string $disk
    ): string {
        $manager = ImageManager::usingDriver(Driver::class);

        $image = $manager->decode(
            $file->getRealPath()
        );

        $image->cover(400, 400);

        $imageData = $image
            ->encodeUsingFormat(Format::WEBP, quality: 85)
            ->toString();

        $path = sprintf(
            'profile-images/users/%d/avatar.webp',
            $user->id
        );

        Storage::disk($disk)->put(
            $path,
            $imageData
        );

        return $path;
    }
}
