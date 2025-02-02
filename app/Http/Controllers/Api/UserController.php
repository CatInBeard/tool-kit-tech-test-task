<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ErrorJsonException;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserIndexRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{

    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     *
     * @throws ErrorJsonException
     */
    public function index(UserIndexRequest $request): \Illuminate\Http\JsonResponse
    {

        $limit = $request->input('limit', 10);
        $page = $request->input('page', 1);

        $filters = $request->except(['limit', 'page']);

        $questionaries = $this->userService->get($limit, $page, $filters);

        return response()->json($questionaries);
    }

    /**
     *
     * @throws ErrorJsonException
     */
    public function store(Request $request): void
    {
        $this->userService->create();
    }

    /**
     *
     * @throws ErrorJsonException
     */
    public function show(string $id): \Illuminate\Http\JsonResponse
    {
        return response()->json($this->userService->getById($id));
    }

    public function showMe(): \Illuminate\Http\JsonResponse
    {
        return response()->json($this->userService->getMe());
    }

    public function questionary()
    {
        return response()->json($this->userService->getMyQuestionary());
    }

    public function update(UserUpdateRequest $request, string $id)
    {
        return response()->json($this->userService->update($id, $request->validated()));
    }


    public function destroy(string $id)
    {
        return response()->json($this->userService->delete($id));
    }
}
