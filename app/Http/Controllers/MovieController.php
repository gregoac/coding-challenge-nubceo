<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;

use App\Models\Movie;
use App\Models\Director;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MovieController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $movies = Movie::filterAndSort($request->get('name'), $request->get('director_name'), $request->get('sort_field'), $request->get('sort_order'));
        $movies->load('actors');

        if ($movies->isEmpty()) {
            return response()->json(['message' => 'No movies found'], 200);
        }

        return response()->json($movies);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255|unique:movies',
        ];

        if($request->has('director_name')){
            $rules['director_name'] = 'required|string|max:255';
        }
        
        if ($request->has('actors_name')) {
            $rules['actors_name.*'] = 'required|string|max:255';
        }

        $validator = Validator::make($request->all(), $rules);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $validatedData = $validator->validated();
    
        try {
            $movie = new Movie;
            $movie->setAttributes($validatedData['name'], $validatedData['director_name']);
            $movie->save();
            $movie->setRelations($validatedData['actors_name']);
            $movie->load('actors');
            
            return response()->json(['message' => 'Movie created successfully', 'movie' => $movie], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'There was an unknown error while trying to create the movie.', 'error' => $e->getMessage()], 500);
        }
    }
}
