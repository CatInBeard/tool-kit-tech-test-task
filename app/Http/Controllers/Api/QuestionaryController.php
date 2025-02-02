<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ErrorJsonException;
use App\Http\Controllers\Controller;
use App\Http\Requests\QuestionaryIndexRequest;
use App\Http\Requests\QuestionaryCreateRequest; // Создайте этот Request для валидации
use App\Http\Requests\QuestionaryUpdateRequest;
use App\Services\QuestionaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class QuestionaryController extends Controller
{
    protected QuestionaryService $questionaryService;

    public function __construct(QuestionaryService $questionaryService)
    {
        $this->questionaryService = $questionaryService;
    }

    /**
     * @group Questionary Management
     *
     * APIs for managing users
     */


    /**
     * Create questionary
     *
     * You can create questionary to create a user
     *
     *
     * @response 201 {
     * "id": 1,
     * "name": "John Doe",
     * "email": "john@example.com"
     * "tg_name": "johnDoe"
     * "phone": "9998887766",
     * "cat_photo" "http://example.com/image.jpg"
     * }
     *
     * @response 401 {
     * "error": "Unauthenticated"
     * }
     * @response 403 {
     * "error": "Unauthorized"
     * }
     *
     * @bodyParam name string optional required The name of the user.
     * @bodyParam email string optional required The email of the user.
     * @bodyParam password string optional The password of the user.
     * @bodyParam tg_name string optional The telegram username of the user.
     * @bodyParam phone string optional The phone number of the user.
     * @bodyParam cat_photo file required The cat photo to upload. Must be an image and less than 8 MB.
     *
     * @throws ErrorJsonException
     */
    public function store(QuestionaryCreateRequest $request): JsonResponse
    {
        try {
            $catPhotoPath = null;

            if ($request->hasFile('catPhoto')) {
                $catPhotoPath = $request->file('catPhoto')->store('cat_photos', 'public');
            }

            $questionary = $this->questionaryService->create(
                $request->input('name'),
                $request->input('email'),
                $request->input('password'),
                $catPhotoPath,
                $request->input('phone'),
                $request->input('tg_name')
            );

            return response()->json($questionary, 201);
        } catch (ErrorJsonException $e) {
            if ($catPhotoPath) {
                Storage::delete($catPhotoPath);
            }
            throw $e;
        }
    }

    /**
     * Confirm questionary
     *
     * Admin can confirm questionary to create a user
     *
     *
     * @response 201 {
     * "message": "confirmed",
     * }
     *
     * @response 401 {
     * "error": "Unauthenticated"
     * }
     * @response 403 {
     * "error": "Unauthorized"
     * }
     *
     *
     * @throws ErrorJsonException
     */
    public function confirm($id): JsonResponse
    {
        $this->questionaryService->confirm($id);
        return response()->json(['message' => 'confirmed'], 201);
    }

    /**
     * Get a list of all questionary
     *
     * Available only for admin
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
    public function index(QuestionaryIndexRequest $request): JsonResponse
    {
        $limit = $request->input('limit', 10);
        $page = $request->input('page', 1);

        $filters = $request->except(['limit', 'page']);

        $questionaries = $this->questionaryService->get($limit, $page, $filters);

        return response()->json($questionaries);
    }

    /**
     * Get questionary by id
     *
     * Available only for admin or to see yourself
     *
     * @header Authorization Bearer token
     *
     * @response 200 {
     *      "id": 1,
     *      "name": "John Doe",
     *      "email": "john@example.com"
     *      "tg_name": "johnDoe"
     *      "phone": "9998887766",
     *      "cat_photo" "http://example.com/image.jpg"
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
    public function show($id): JsonResponse
    {
        $questionary = $this->questionaryService->getById($id);
        return response()->json($questionary);
    }

    /**
     * Update questionary
     *
     * Available only for admin or to edit yourself
     *
     * @header Authorization Bearer token
     *
     * @response 200 {
     *      "id": 1,
     *      "name": "John Doe",
     *      "email": "john@example.com"
     *      "tg_name": "johnDoe"
     *      "phone": "9998887766",
     *      "cat_photo" "http://example.com/image.jpg"
     * }
     *
     * @response 401 {
     *   "error": "Unauthenticated"
     * }
     * @response 403 {
     *   "error": "Unauthorized"
     * }
     *
     *
     * @bodyParam name string optional required The name of the user.
     * @bodyParam email string optional required The email of the user.
     * @bodyParam password string optional The password of the user.
     * @bodyParam tg_name string optional The telegram username of the user.
     * @bodyParam phone string optional The phone number of the user.
     * @bodyParam cat_photo file required The cat photo to upload. Must be an image and less than 8 MB.
     *
     * @throws ErrorJsonException
     */
    public function update(QuestionaryUpdateRequest $request, $id): JsonResponse
    {
        $data = $request->validated();
        $catPhotoPath = null;

        if ($request->hasFile('catPhoto')) {
            $catPhotoPath = $request->file('catPhoto')->store('cat_photos', 'public');
        }

        if ($catPhotoPath) {
            $data['cat_photo'] = $catPhotoPath;
        }

        $questionary = $this->questionaryService->update($id, $data);
        return response()->json($questionary);
    }

    /**
     * Update questionary
     *
     * Available only for admin or to edit yourself
     *
     * @header Authorization Bearer token
     *
     * @response 204 {
     *      "message": "successfully deleted",
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

    public function destroy($id): JsonResponse
    {
        $this->questionaryService->delete($id);
        return response()->json(["message" => "successfully deleted"], 204);
    }
}
