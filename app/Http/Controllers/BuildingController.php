<?php

namespace App\Http\Controllers;

use App\Http\Requests\BuildingUpdateRequest;
use App\Models\Building;
use Illuminate\Http\Request;

class BuildingController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/buildings",
     *     summary="Получить список зданий",
     *     tags={"Buildings"},
     *     @OA\Response(
     *         response=200,
     *         description="Список зданий успешно получен",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Building"))
     *     )
     * )
     */
    public function index()
    {
        $building = Building::query()->orderByDesc('id')->get();
        return response()->json($building, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/buildings",
     *     summary="Создать новое здание",
     *     tags={"Buildings"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"address", "latitude", "longitude"},
     *             @OA\Property(property="address", type="string", example="г. Москва, ул. Ленина 1, офис 3"),
     *             @OA\Property(property="latitude", type="number", format="float", example=55.751244),
     *             @OA\Property(property="longitude", type="number", format="float", example=37.618423)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Здание успешно создано",
     *         @OA\JsonContent(ref="#/components/schemas/Building")
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'address' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $building = Building::create($validated);

        return response()->json($building, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/buildings/{id}",
     *     summary="Получить информацию о здании",
     *     tags={"Buildings"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Информация о здании",
     *         @OA\JsonContent(ref="#/components/schemas/Building")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Здание не найдено",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Здание не найдено"))
     *     )
     * )
     */
    public function show($id)
    {
        $building = Building::find($id);

        if (!$building) {
            return response()->json(['message' => 'Здание не найдено'], 404);
        }

        return response()->json($building, 200);
    }

    /**
     * @OA\Put(
     *     path="/api/buildings/{id}",
     *     summary="Обновить данные здания",
     *     tags={"Buildings"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/BuildingUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Обновленные данные здания",
     *         @OA\JsonContent(ref="#/components/schemas/Building")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Здание не найдено",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Здание не найдено"))
     *     )
     * )
     */
    public function update(BuildingUpdateRequest $request, $id)
    {
        $building = Building::find($id);

        if (!$building) {
            return response()->json(['message' => 'Здание не найдено'], 404);
        }

        $building->update($request->validated());

        return response()->json($building, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/buildings/{id}",
     *     summary="Удалить здание",
     *     tags={"Buildings"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Здание успешно удалено",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Здание успешно удалено"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Здание не найдено",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Здание не найдено"))
     *     )
     * )
     */
    public function destroy($id)
    {
        $building = Building::find($id);

        if (!$building) {
            return response()->json(['message' => 'Здание не найдено'], 404);
        }

        $building->delete();

        return response()->json(['message' => 'Здание успешно удалено'], 200);
    }
}
