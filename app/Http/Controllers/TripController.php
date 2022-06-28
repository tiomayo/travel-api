<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use PDO;

class TripController extends Controller
{
    protected $user;

    public function __construct()
    {
        try {
            $this->user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            return $e;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'title' => 'nullable|string',
        ]);

        $trips = Cache::remember($this->user->id, 60 * 60, function () {
            return $this->user->trips()->get();
        });

        if (!empty($request['title'])) {
            $trips = $trips->filter(function ($trip) use ($request) {
                return str_contains(strtolower($trip->title), strtolower($request['title']));
            })->all();
        }

        return $trips;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:50',
            'origin' => 'required|string|max:30',
            'destination' => 'required|string|max:30',
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
            'type' => 'required|string',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $validated = $validator->safe()->only(['title', 'origin', 'destination', 'start', 'end', 'type', 'description']);

        $trip = $this->user->trips()->create([
            'title' => $validated['title'],
            'origin' => $validated['origin'],
            'destination' => $validated['destination'],
            'start' => $validated['start'],
            'end' => $validated['end'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Trip created successfully',
            'data' => $trip
        ], Response::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $trip = $this->user->trips()->where('id', $id)->first();
        if (null == $trip) {
            return response()->json([
                'success' => false,
            ], Response::HTTP_NO_CONTENT);
        }
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:50',
            'origin' => 'required|string|max:30',
            'destination' => 'required|string|max:30',
            'start' => 'required|date',
            'end' => 'required|date|after_or_equal:start',
            'type' => 'required|string',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $validated = $validator->safe()->only(['title', 'origin', 'destination', 'start', 'end', 'type', 'description']);

        $trip = $trip->update([
            'title' => $validated['title'],
            'origin' => $validated['origin'],
            'destination' => $validated['destination'],
            'start' => $validated['start'],
            'end' => $validated['end'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Trip updated successfully',
            'data' => $trip,
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $trip = $this->user->trips()->where('id', $id)->first();
        if (null == $trip) {
            return response()->json([
                'success' => false,
            ], Response::HTTP_NO_CONTENT);
        }
        $trip->delete();

        return response()->json([
            'success' => true,
            'message' => 'Trip deleted successfully'
        ], Response::HTTP_OK);
    }
}
