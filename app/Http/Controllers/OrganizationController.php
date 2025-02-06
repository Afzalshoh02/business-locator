<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Building;
use App\Models\Organization;
use Illuminate\Http\Request;
/**
 * @OA\Get(
 *     path="/api/organizations",
 *     summary="Получить список организаций",
 *     security={{"apiKey": {}}},
 *     @OA\Response(
 *         response=200,
 *         description="Список организаций",
 *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Organization"))
 *     ),
 *     @OA\Response(response=401, description="Unauthorized")
 * )
 */
/**
 * @OA\Schema(
 *     schema="OrganizationCreateRequest",
 *     required={"name", "phone_numbers", "building_id", "activities"},
 *     @OA\Property(property="name", type="string", example="Название компании"),
 *     @OA\Property(property="phone_numbers", type="array", @OA\Items(type="string"), example={"123-456", "789-012"}),
 *     @OA\Property(property="building_id", type="integer", example=5),
 *     @OA\Property(property="activities", type="array", @OA\Items(type="integer"), example={1, 2, 3})
 * )
 *
 * @OA\Schema(
 *     schema="OrganizationUpdateRequest",
 *     @OA\Property(property="name", type="string", example="Новое название"),
 *     @OA\Property(property="phone_numbers", type="array", @OA\Items(type="string"), example={"2-222-222", "3-333-333"}),
 *     @OA\Property(property="building_id", type="integer", example=6),
 *     @OA\Property(property="activities", type="array", @OA\Items(type="integer"), example={2, 3, 4})
 * )
 */

class OrganizationController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/organizations",
     *     summary="Получить список организаций",
     *     tags={"Organizations"},
     *     @OA\Response(
     *         response=200,
     *         description="Список организаций",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Organization"))
     *     )
     * )
     */
    public function index()
    {
        return Organization::with(['building', 'activities'])->orderByDesc('id')->get();
    }

    /**
     * @OA\Post(
     *     path="/api/organizations",
     *     summary="Создать новую организацию",
     *     tags={"Organizations"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"name", "phone_numbers", "building_id", "activities"},
     *                 @OA\Property(property="name", type="string", example="Название компании"),
     *                 @OA\Property(
     *                     property="phone_numbers",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"2-222-222", "3-333-333"}
     *                 ),
     *                 @OA\Property(property="building_id", type="integer", example=5),
     *                 @OA\Property(
     *                     property="activities",
     *                     type="array",
     *                     @OA\Items(type="integer"),
     *                     example={1, 2}
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Созданная организация",
     *         @OA\JsonContent(ref="#/components/schemas/Organization")
     *     )
     * )
     */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_numbers' => 'required|array',
            'phone_numbers.*' => 'string',
            'building_id' => 'required|exists:buildings,id',
            'activities' => 'required|array',
            'activities.*' => 'integer|exists:activities,id',
        ]);

        $organization = Organization::create([
            'name' => $validated['name'],
            'phone_numbers' => json_encode($validated['phone_numbers']),
            'building_id' => $validated['building_id'],
        ]);

        $organization->activities()->sync($validated['activities']);

        return response()->json($organization->load('building', 'activities'), 201);
    }


    /**
     * @OA\Get(
     *     path="/api/organizations/{id}",
     *     summary="Получить данные организации",
     *     tags={"Organizations"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Данные организации",
     *         @OA\JsonContent(ref="#/components/schemas/Organization")
     *     )
     * )
     */
    public function show($id)
    {
        $organization = Organization::with('building', 'activities')->find($id);

        if (!$organization) {
            return response()->json(['message' => 'Организация не найдена'], 404);
        }

        return response()->json($organization);
    }

    /**
     * @OA\Put(
     *     path="/api/organizations/{id}",
     *     summary="Обновить данные организации",
     *     tags={"Organizations"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/OrganizationUpdateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Обновленная организация",
     *         @OA\JsonContent(ref="#/components/schemas/Organization")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Организация не найдена",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Организация не найдена")
     *         )
     *     )
     * )
     */
    public function update(Request $request, $id)
    {
        $organization = Organization::with('building', 'activities')->find($id);

        if (!$organization) {
            return response()->json(['message' => 'Организация не найдена'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone_numbers' => 'sometimes|array',
            'building_id' => 'sometimes|exists:buildings,id',
            'activities' => 'sometimes|array',
            'activities.*' => 'exists:activities,id',
        ]);

        $organization->update($validated);

        if ($request->has('activities')) {
            $organization->activities()->sync($request->activities);
        }

        return response()->json($organization->load('building', 'activities'));
    }

    /**
     * @OA\Delete(
     *     path="/api/organizations/{id}",
     *     summary="Удалить организацию",
     *     tags={"Organizations"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Организация успешно удалена",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Организация успешно удалена")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Организация не найдена",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Организация не найдена")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $organization = Organization::find($id);

        if (!$organization) {
            return response()->json(['message' => 'Организация не найдена'], 404);
        }

        $organization->delete();

        return response()->json(['message' => 'Организация успешно удалена'], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/organizations/building/{buildingId}",
     *     summary="Получить список организаций в здании",
     *     tags={"Organizations"},
     *     @OA\Parameter(
     *         name="buildingId",
     *         in="path",
     *         required=true,
     *         description="ID здания",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список организаций в здании",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Organization"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Здание не найдено",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Здание не найдено")
     *         )
     *     )
     * )
     */
    public function organizationsByBuilding($buildingId)
    {
        $building = Building::find($buildingId);
        if (!$building) {
            return response()->json(['message' => 'Здание не найдено'], 404);
        }
        return response()->json($building->organizations, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/organizations/activity/{activityId}",
     *     summary="Получить список организаций по виду деятельности",
     *     tags={"Organizations"},
     *     @OA\Parameter(
     *         name="activityId",
     *         in="path",
     *         required=true,
     *         description="ID вида деятельности",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список организаций, связанных с видом деятельности",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Organization"))
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Вид деятельности не найден",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Вид деятельности не найден")
     *         )
     *     )
     * )
     */
    public function organizationsByActivity($activityId)
    {
        $activity = Activity::find($activityId);
        if (!$activity) {
            return response()->json(['message' => 'Вид деятельности не найден'], 404);
        }
        return response()->json($activity->organizations, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/organizations/radius",
     *     summary="Получить список организаций в радиусе",
     *     tags={"Organizations"},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Параметры поиска",
     *         @OA\JsonContent(
     *             required={"latitude", "longitude", "radius"},
     *             @OA\Property(property="latitude", type="number", format="float", example=-90.0, description="Широта центра поиска"),
     *             @OA\Property(property="longitude", type="number", format="float", example=180.0, description="Долгота центра поиска"),
     *             @OA\Property(property="radius", type="number", format="float", example=100, description="Радиус поиска в километрах")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список организаций в указанном радиусе",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Organization"))
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Ошибка валидации",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Ошибка валидации"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function organizationsInRadius(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|numeric',
        ]);

        $latitude = $validated['latitude'];
        $longitude = $validated['longitude'];
        $radius = $validated['radius'];

        $organizations = Organization::whereHas('building', function ($query) use ($longitude, $latitude, $radius) {
            $query->whereRaw(
                'ST_Distance_Sphere(point(longitude, latitude), point(?, ?)) <= ?',
                [$longitude, $latitude, $radius * 1000]
            );
        })
            ->with('building')
            ->get();

        return response()->json($organizations, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/organizations/search/activity/{activityId}",
     *     summary="Получить список организаций по виду деятельности",
     *     tags={"Organizations"},
     *     @OA\Parameter(
     *         name="activityId",
     *         in="path",
     *         required=true,
     *         description="ID вида деятельности",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список организаций, связанных с видом деятельности",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Organization")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Вид деятельности не найден",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Вид деятельности не найден")
     *         )
     *     )
     * )
     */
    public function searchOrganizationsByActivity($activityId)
    {
        $activity = Activity::with('children')->find($activityId);

        if (!$activity) {
            return response()->json(['message' => 'Вид деятельности не найден'], 404);
        }

        $activityIds = $this->getAllChildActivityIds($activity);

        $organizations = Organization::whereIn('id', function ($query) use ($activityIds) {
            $query->select('organization_id')
                ->from('organization_activity')
                ->whereIn('activity_id', $activityIds);
        })->get();

        return response()->json($organizations, 200);
    }

    private function getAllChildActivityIds(Activity $activity)
    {
        $activityIds = [$activity->id];
        foreach ($activity->children as $child) {
            $activityIds = array_merge($activityIds, $this->getAllChildActivityIds($child));
        }
        return $activityIds;
    }

    /**
     * @OA\Get(
     *     path="/api/organizations/search/name/{name}",
     *     summary="Поиск организаций по имени",
     *     tags={"Organizations"},
     *     @OA\Parameter(
     *         name="name",
     *         in="path",
     *         required=true,
     *         description="Частичное или полное имя организации",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список организаций, соответствующих имени",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Organization")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Организации не найдены",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Организации не найдены")
     *         )
     *     )
     * )
     */

    public function searchOrganizationByName($name)
    {
        $organizations = Organization::with('building')->where('name', 'like', '%' . $name . '%')->get();
        return response()->json($organizations, 200);
    }

    /**
     * @OA\Get(
     *     path="/api/organizations/search/activity/limited/{activityId}",
     *     summary="Поиск организаций по виду деятельности с ограничением на 3 уровня вложенности",
     *     tags={"Organizations"},
     *     @OA\Parameter(
     *         name="activityId",
     *         in="path",
     *         required=true,
     *         description="ID вида деятельности",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Список организаций, соответствующих виду деятельности с ограничением на 3 уровня вложенности",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Organization")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Вид деятельности не найден",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Вид деятельности не найден")
     *         )
     *     )
     * )
     */

    public function searchOrganizationsWithLimitedActivityDepth($activityId)
    {
        $activity = Activity::with('children')->find($activityId);
        if (!$activity) {
            return response()->json(['message' => 'Вид деятельности не найден'], 404);
        }

        $activityIds = $this->getLimitedDepthChildActivityIds($activity, 3);

        $organizations = Organization::whereIn('id', function ($query) use ($activityIds) {
            $query->select('organization_id')
                ->from('organization_activity')
                ->whereIn('activity_id', $activityIds);
        })->get();

        return response()->json($organizations, 200);
    }

    private function getLimitedDepthChildActivityIds(Activity $activity, $maxDepth, $currentDepth = 1)
    {
        $activityIds = [$activity->id];
        if ($currentDepth < $maxDepth) {
            foreach ($activity->children as $child) {
                $activityIds = array_merge($activityIds, $this->getLimitedDepthChildActivityIds($child, $maxDepth, $currentDepth + 1));
            }
        }
        return $activityIds;
    }

}
