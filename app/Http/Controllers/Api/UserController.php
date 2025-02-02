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
     * @group User Management
     *
     * APIs for managing users
     */

    /**
     * Get a list of users
     *
     * Only users with admin role can use this endpoint
     *
     * @header Authorization Bearer token
     *
     * @response 200 [
     *    {
     *      "id": 1,
     *      "name": "John Doe",
     *      "email": "john@example.com"
     *      "tg_name": "johnDoe"
     *      "phone": "9998887766"
     *    },
     *    {
     *      "id": 2,
     *      "name": "Jane Smith",
     *      "email": "jane@example.com"
     *      "tg_name": "johnDoe"
     *      "phone": "9998887766"
     *    }
     *  ]
     *
     * @response 401 {
     *   "error": "Unauthenticated"
     * }
     * @response 403 {
     *   "error": "Unauthorized"
     * }
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
     * Create user
     *
     * You can't create user from api, you need to confirm questionary
     *
     * @header Authorization Bearer token
     *
     *
     * @response 401 {
     *   "error": "Unauthenticated"
     * }
     * @response 403 {
     *   "error": "Unauthorized"
     * }
     * @throws ErrorJsonException
     */
    public function store(Request $request): void
    {
        $this->userService->create();
    }

    /**
     * Show user
     *
     * You can get user by id. If not admin, you can get only yourself
     *
     * @header Authorization Bearer token
     *
     * @response 200 {
     *      "id": 1,
     *      "name": "John Doe",
     *      "email": "john@example.com"
     *      "tg_name": "johnDoe"
     *      "phone": "9998887766"
     * }
     *
     * @response 401 {
     *   "error": "Unauthenticated"
     * }
     * @response 403 {
     *   "error": "Unauthorized"
     * }
     *
     * @throws ErrorJsonException
     */
    public function show(string $id): \Illuminate\Http\JsonResponse
    {
        return response()->json($this->userService->getById($id));
    }
    /**
     * Get my user
     *
     * You can see yourself
     *
     * @header Authorization Bearer token
     *
     * @response 200 {
     *      "id": 1,
     *      "name": "John Doe",
     *      "email": "john@example.com"
     *      "tg_name": "johnDoe"
     *      "phone": "9998887766"
     * }
     *
     * @response 401 {
     *   "error": "Unauthenticated"
     * }
     * @response 403 {
     *   "error": "Unauthorized"
     * }
     *
     */

    public function showMe(): \Illuminate\Http\JsonResponse
    {
        return response()->json($this->userService->getMe());
    }

    /**
     * Get a list of users questionary
     *
     * You can see your questionary if not admin
     *
     * @header Authorization Bearer token
     *
     * @response 200 [{
     *      "id": 1,
     *      "name": "John Doe",
     *      "email": "john@example.com"
     *      "tg_name": "johnDoe"
     *      "phone": "9998887766",
     *      "cat_photo" "http://example.com/image.jpg"
     * }]
     *
     * @response 401 {
     *   "error": "Unauthenticated"
     * }
     * @response 403 {
     *   "error": "Unauthorized"
     * }
     *
     */
    public function questionary(): \Illuminate\Http\JsonResponse
    {
        return response()->json($this->userService->getMyQuestionary());
    }

    /**
     * Update user info
     *
     * You can see update only yourself if not admin
     *
     * @header Authorization Bearer token
     *
     * @response 200 {
     *      "id": 1,
     *      "name": "John Doe",
     *      "email": "john@example.com"
     *      "tg_name": "johnDoe"
     *      "phone": "9998887766",
     * }
     *
     * @response 401 {
     *   "error": "Unauthenticated"
     * }
     * @response 403 {
     *   "error": "Unauthorized"
     * }
     *
     * @bodyParam name string optional required The name of the user.
     * @bodyParam email string optional required The email of the user.
     * @bodyParam password string optional The password of the user.
     * @bodyParam tg_name string optional The telegram username of the user.
     * @bodyParam phone string optional The phone number of the user.
     * @throws ErrorJsonException
     */
    public function update(UserUpdateRequest $request, string $id): \Illuminate\Http\JsonResponse
    {
        return response()->json($this->userService->update($id, $request->validated()));
    }

    /**
     * Delete user by id
     *
     * You can see delete only yourself if not admin
     *
     * @header Authorization Bearer token
     *
     * @response 204 {
     *  "message" : "user deleted successfully"
     * }
     *
     * @response 401 {
     *   "error": "Unauthenticated"
     * }
     * @response 403 {
     *   "error": "Unauthorized"
     * }
     *
     * @throws ErrorJsonException
     */

    public function destroy(string $id): \Illuminate\Http\JsonResponse
    {
        $this->userService->delete($id);
        return response()->json(["message" => "user deleted successfully"], 204);
    }
}
