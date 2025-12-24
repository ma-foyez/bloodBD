<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\District;
use App\Models\Division;
use App\Models\Union;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends ApiController
{
    /**
     * Get all divisions.
     */
    public function getDivisions(): JsonResponse
    {
        $divisions = Division::all(['id', 'name', 'bn_name', 'url']);
        return $this->successResponse($divisions, 'Divisions retrieved successfully.');
    }

    /**
     * Get districts by division ID.
     */
    public function getDistricts($divisionId): JsonResponse
    {
        $districts = District::where('division_id', $divisionId)
            ->get(['id', 'division_id', 'name', 'bn_name', 'url']);

        return $this->successResponse($districts, 'Districts retrieved successfully.');
    }

    /**
     * Get areas by district ID.
     */
    public function getAreas($districtId): JsonResponse
    {
        $areas = Area::where('district_id', $districtId)
            ->get(['id', 'district_id', 'name', 'bn_name', 'url']);

        return $this->successResponse($areas, 'Areas retrieved successfully.');
    }

    /**
     * Get unions by area ID.
     */
    public function getUnions($areaId): JsonResponse
    {
        $unions = Union::where('area_id', $areaId)
            ->get(['id', 'area_id', 'name', 'bn_name', 'url']);

        return $this->successResponse($unions, 'Unions retrieved successfully.');
    }
}
