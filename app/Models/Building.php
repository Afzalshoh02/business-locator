<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * @OA\Schema(
 *     schema="Building",
 *     title="Building",
 *     description="Модель здания",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="address", type="string", example="г. Москва, ул. Ленина 1, офис 3"),
 *     @OA\Property(property="latitude", type="number", format="float", example=55.751244),
 *     @OA\Property(property="longitude", type="number", format="float", example=37.618423),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-05T12:34:56Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-05T12:34:56Z")
 * )
 */

class Building extends Model
{
    use HasFactory;

    protected $table = 'buildings';
    protected $fillable = [
        'address',
        'latitude',
        'longitude'
    ];
    public function organizations()
    {
        return $this->hasMany(Organization::class);
    }
}
