<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\OrganizationController;
use App\Models\Organization;
use Illuminate\Support\Facades\Route;

Route::apiResource('organizations', OrganizationController::class);
Route::apiResource('buildings', BuildingController::class);
Route::apiResource('activities', ActivityController::class);

Route::get('/organizations/building/{buildingId}', [OrganizationController::class, 'organizationsByBuilding']);
Route::get('/organizations/activity/{activityId}', [OrganizationController::class, 'organizationsByActivity']);
Route::post('/organizations/radius', [OrganizationController::class, 'organizationsInRadius']);
Route::get('/organizations/search/activity/{activityId}', [OrganizationController::class, 'searchOrganizationsByActivity']);
Route::get('/organizations/search/name/{name}', [OrganizationController::class, 'searchOrganizationByName']);
Route::get('/organizations/search/activity/limited/{activityId}', [OrganizationController::class, 'searchOrganizationsWithLimitedActivityDepth']);


