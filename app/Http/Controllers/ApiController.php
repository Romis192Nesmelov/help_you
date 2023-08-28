<?php

namespace App\Http\Controllers;

use App\Models\Point;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function getPoints(): JsonResponse
    {
        $points = Point::all();
        $jsonPoints = [];
        foreach ($points as $point) {
            $jsonPoints[] = [
                'type' => 'Feature',
                'id' => $point->id,
                'name' => $point->name,
                'description' => $point->description,
                'geometry' => ['type' => 'Point', 'coordinates' => [$point->latitude, $point->longitude]],
                'properties' => ['hintContent' => $point->name]
            ];
        }
        return response()->json($jsonPoints);
    }
}
