<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/activities",
     *     summary="Получить список всех видов деятельности",
     *     tags={"Activities"},
     *     @OA\Response(
     *         response=200,
     *         description="Список видов деятельности",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Activity"))
     *     )
     * )
     */
    public function index()
    {
        return response()->json(Activity::with('children')->orderByDesc('id')->get(), 200);
    }

    /**
     * @OA\Post(
     *     path="/api/activities",
     *     summary="Создать новый вид деятельности",
     *     tags={"Activities"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Программирование"),
     *             @OA\Property(property="parent_id", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Вид деятельности создан",
     *         @OA\JsonContent(ref="#/components/schemas/Activity")
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:activities,id',
        ]);

        $activity = Activity::create($validated);

        return response()->json($activity, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/activities/{id}",
     *     summary="Получить информацию о виде деятельности",
     *     tags={"Activities"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Информация о виде деятельности",
     *         @OA\JsonContent(ref="#/components/schemas/Activity")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Вид деятельности не найден",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Вид деятельности не найден"))
     *     )
     * )
     */
    public function show(Activity $activity)
    {
        return response()->json($activity->load('children'), 200);
    }

    /**
     * @OA\Put(
     *     path="/api/activities/{id}",
     *     summary="Обновить вид деятельности",
     *     tags={"Activities"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", example="Новые технологии"),
     *             @OA\Property(property="parent_id", type="integer", example=2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Вид деятельности обновлен",
     *         @OA\JsonContent(ref="#/components/schemas/Activity")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Вид деятельности не найден",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Вид деятельности не найден"))
     *     )
     * )
     */
    public function update(Request $request, Activity $activity)
    {
        if (!$activity) {
            return response()->json(['message' => 'Вид деятельности не найден'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'parent_id' => 'nullable|exists:activities,id',
        ]);

        $activity->update($validated);

        return response()->json($activity, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/activities/{id}",
     *     summary="Удалить вид деятельности",
     *     tags={"Activities"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Вид деятельности удален"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Невозможно удалить вид деятельности с подкатегориями",
     *         @OA\JsonContent(@OA\Property(property="error", type="string", example="Нельзя удалить категорию, у которой есть подкатегории"))
     *     )
     * )
     */
    public function destroy($id)
    {
        $activity = Activity::find($id);
        if (!$activity) {
            return response()->json(['message' => 'Вид деятельности не найден'], 404);
        }

        if ($activity->children()->exists()) {
            return response()->json(['error' => 'Нельзя удалить категорию, у которой есть подкатегории'], 400);
        }

        $activity->delete();
        return response()->json(['message' => 'Вид деятельности удален'], 200);
    }

}
