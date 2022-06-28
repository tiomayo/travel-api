<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;

class TripController extends Controller
{
    protected $user;

    public function __construct()
    {
        try {
            $this->user = JWTAuth::parseToken()->authenticate();
        } catch(Exception $e) {
            return $e;
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->user
            ->trips()
            ->get();
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
