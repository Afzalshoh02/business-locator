<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrganizationActivity extends Model
{
    protected $table = 'organization_activity';
    protected $fillable = [
        'organization_id',
        'activity_id',
    ];

}
