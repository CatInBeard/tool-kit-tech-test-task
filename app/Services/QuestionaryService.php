<?php

namespace App\Services;

use App\Exceptions\ErrorJsonException;
use App\Models\Questionary;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class QuestionaryService
{
    /**
     * @throws ErrorJsonException
     */
    public function create($name, $email, $password, $catPhoto = null, $phone = null, $tg_name = null)
    {
        try {
            $password = Hash::make($password);
            return Questionary::create(compact('name', 'email', 'password', 'catPhoto', 'phone', 'tg_name'));
        } catch (Exception $e) {
            throw new ErrorJsonException('Could not create questionary', 500);
        }
    }

    /**
     * @throws ErrorJsonException
     */
    public function get($limit = 100, $page = 1, $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $currentUser = Auth::user();
        if (!$currentUser->isAdmin()) {
            throw new ErrorJsonException('Unauthorized to list questionaries', 403);
        }
        $query = Questionary::query();

        foreach ($filters as $field => $value) {
            if ($value !== null) {
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
        $questionary =  Questionary::findOrFail($id);
        $currentUser = Auth::user();
        if ($currentUser->isAdmin() || $questionary->isOwner($currentUser)) {
            return $questionary;
        }

        throw new ErrorJsonException('Unauthorized to access this questionary', 403);
    }

    /**
     * @throws ErrorJsonException
     */
    public function update($id, array $data)
    {
        $questionary =  Questionary::findOrFail($id);
        $currentUser = Auth::user();
        if (!$currentUser->isAdmin() && $currentUser->id !== $questionary->user_id) {
            throw new ErrorJsonException('Unauthorized to update questionary', 403);
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $questionary->update($data);
        return $questionary;
    }

    /**
     * @throws ErrorJsonException
     */
    public function delete($id): void
    {
        $currentUser = Auth::user();
        if (!$currentUser->isAdmin()) {
            throw new ErrorJsonException('Unauthorized to delete questionary', 403);
        }

        $questionary = $this->getById($id);

        $questionary->delete();
    }

    /**
     * @throws ErrorJsonException
     */
    public function confirm($id)
    {
        $currentUser = Auth::user();
        if (!$currentUser->isAdmin()) {
            throw new ErrorJsonException('Unauthorized to confirm', 403);
        }

        $questionary =  Questionary::findOrFail($id);

        $user = User::createFromQuestionary($questionary);

        $questionary->user_id = $user->id;
    }
}
