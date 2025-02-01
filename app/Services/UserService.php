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
    public function create()
    {
        throw new ErrorJsonException('could not create user, you should use questionary instead', 403);
    }

    /**
     * @throws ErrorJsonException
     */
    public function get($limit = 100, $page = 1, $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = User::query();

        foreach ($filters as $field => $value) {
            if (!empty($value)) {
                $query->where($field, 'LIKE', '%' . $value . '%');
            }
        }

        return $query->paginate($limit, ['*'], 'page', $page);
    }

    /**
     * @throws ErrorJsonException
     */
    public function getById($id)
    {
        $currentUser = Auth::user();
        $user = User::findOrFail($id);
        if(!$this->isAuthorizeToAccess($currentUser, $user)){
            throw new ErrorJsonException('Unauthorized to to access this user', 403);
        }

        return $user;

    }

    /**
     * @throws ErrorJsonException
     */
    public function update($id, array $data)
    {
        $user = $this->getById($id);
        $currentUser = Auth::user();

        if(!$this->isAuthorizeToAccess($currentUser, $user)){
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

    private function isAuthorizeToAccess($currentUser, $user): bool
    {
        return $this->isCurrentUser($currentUser, $user) && $currentUser->isAdmin();
    }

    private function isCurrentUser($currentUser, $user): bool
    {
        return $currentUser->id === $user->id;
    }

}
