<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Activity",
 *     title="Activity",
 *     description="Модель деятельности",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Программирование"),
 *     @OA\Property(property="parent_id", type="integer", example=null),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-02-05T10:00:00"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-02-05T10:00:00")
 * )
 */

class Activity extends Model
{
    use HasFactory;

    protected $table = 'activities';
    protected $fillable = [
        'name',
        'parent_id'
    ];
    public function parent()
    {
        return $this->belongsTo(Activity::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Activity::class, 'parent_id');
    }

    public function organizations()
    {
        return $this->belongsToMany(Organization::class, 'organization_activity');
    }
}
