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
                $request->name,
                $request->email,
                $request->password,
                $catPhotoPath,
                $request->phone,
                $request->tg_name
            );

            return response()->json($questionary, 201);
        } catch (ErrorJsonException $e) {
            if ($catPhotoPath) {
                Storage::delete($catPhotoPath);
            }
            throw $e;
        }
    }

    public function confirm($id): JsonResponse
    {
        $this->questionaryService->confirm($id);
        return response()->json(['message' => 'confirmed'], 201);
    }

    /**
     * @throws ErrorJsonException
     */
    public function index(QuestionaryIndexRequest $request): JsonResponse
    {
        $limit = $request->input('limit', 10);
        $page = $request->input('page', 1);

        $filters = $request->except(['limit', 'page']);

        $questionaries = $this->questionaryService->get($limit, $page, $filters);

        return response()->json($questionaries);
    }

    public function show($id): JsonResponse
    {
        $questionary = $this->questionaryService->getById($id);
        return response()->json($questionary);
    }

    public function update(QuestionaryUpdateRequest $request, $id): JsonResponse
    {
        try {
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
        } catch (ErrorJsonException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $questionary = $this->questionaryService->delete($id);
            return response()->json($questionary, 204);
        } catch (ErrorJsonException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }
}
