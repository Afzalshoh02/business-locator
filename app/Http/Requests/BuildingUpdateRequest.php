<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @OA\Schema(
 *     schema="BuildingUpdateRequest",
 *     title="Building Update Request",
 *     description="Данные для обновления здания",
 *     @OA\Property(property="address", type="string", example="г. Москва, ул. Ленина 10"),
 *     @OA\Property(property="latitude", type="number", format="float", example=55.75222),
 *     @OA\Property(property="longitude", type="number", format="float", example=37.61556)
 * )
 */

class BuildingUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'address' => 'sometimes|string|max:255',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
        ];
    }
}
