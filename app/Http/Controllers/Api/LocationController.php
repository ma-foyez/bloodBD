<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\District;
use App\Models\Division;
use App\Models\Union;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Get all divisions.
     */
    public function getDivisions(): JsonResponse
    {
        $divisions = Division::all(['id', 'name', 'bn_name', 'url']);
        return response()->json($divisions);
    }

    /**
     * Get districts by division ID.
     */
    public function getDistricts($divisionId): JsonResponse
    {
        $districts = District::where('division_id', $divisionId)
            ->get(['id', 'division_id', 'name', 'bn_name', 'url']);

        return response()->json($districts);
    }

    /**
     * Get areas by district ID.
     */
    public function getAreas($districtId): JsonResponse
    {
        $areas = Area::where('district_id', $districtId)
            ->get(['id', 'district_id', 'name', 'bn_name', 'url']);

        return response()->json($areas);
    }

    /**
     * Get unions by area ID.
     */
    public function getUnions($areaId): JsonResponse
    {
        $unions = Union::where('area_id', $areaId)
            ->get(['id', 'area_id', 'name', 'bn_name', 'url']);

        return response()->json($unions);
    }
}
