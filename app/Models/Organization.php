<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Organization",
 *     title="Организация",
 *     description="Модель организации",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Название компании"),
 *     @OA\Property(property="phone_numbers", type="array", @OA\Items(type="string"), example={"2-222-222", "3-333-333"}),
 *     @OA\Property(property="building_id", type="integer", example=5),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Organization extends Model
{
    use HasFactory;

    protected $table = 'organizations';
    protected $fillable = [
        'name',
        'phone_numbers',
        'building_id'
    ];
    protected $casts = [
        'phone_numbers' => 'array',
    ];
    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'organization_activity');
    }
}
