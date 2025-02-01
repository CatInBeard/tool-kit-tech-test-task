<?php

namespace App\Services;

use App\Exceptions\ErrorJsonException;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserService
{
    /**
     * @throws ErrorJsonException
     */
    public function create($name, $email, $password)
    {
        try {
            return User::create(compact('name', 'email', 'password'));
        } catch (Exception $e) {
            throw new ErrorJsonException('could not create user', 500);
        }
    }

    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return User::all();
    }

    public function getById($id)
    {
        return User::findOrFail($id);
    }

    /**
     * @throws ErrorJsonException
     */
    public function update($id, array $data)
    {
        $user = $this->getById($id);
        $currentUser = Auth::user();

        if(!$this->isAuthorizeUpdate($currentUser, $user)){
            throw new ErrorJsonException('unauthorized to update this user', 403);
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
        return $user;
    }

    /**
     * @throws ErrorJsonException
     */
    public function delete($id)
    {
        $user = $this->getById($id);
        $currentUser = Auth::user();

        if (!$currentUser->isAdmin()) {
            throw new ErrorJsonException('Unauthorized to delete this user', 403);
        }

        $user->delete();
        return $user;
    }

    private function isAuthorizeUpdate($currentUser, $user): bool
    {
        return $this->isCurrentUser($currentUser, $user) && $currentUser->isAdmin();
    }

    private function isCurrentUser($currentUser, $user): bool
    {
        return $currentUser->id === $user->id;
    }

}
