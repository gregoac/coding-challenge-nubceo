<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TVShow;
use App\Models\Director;
use Illuminate\Support\Facades\Validator;
use App\Models\Season;
use App\Models\Episode;

class TVShowController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255|unique:tv_shows',
        ];

        if($request->has('director_name')){
            $rules['director_name'] = 'required|string|max:255';
        }
        
        if ($request->has('seasons')) {
            $rules['seasons.*.season_number'] = 'required|integer|min:1';
            $rules['seasons.*.episodes.*.episode_number'] = 'required|integer|min:1';
            $rules['seasons.*.episodes.*.name'] = 'required|string|max:255';
            $rules['seasons.*.actors'] = 'required|array';
            $rules['seasons.*.actors.*'] = 'required|string|max:255';
        }
        // Validate the request data here
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed.', 'errors' => $validator->errors()], 422);
        }

        try {
            $tvShow = TvShow::createTvShowWithSeasonsAndEpisodes($request->all());
            return response()->json(['message' => 'TV show created successfully', 'tvShow' => $tvShow], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'There was an unknown error while trying to create the TV show.', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($tvShowName, $seasonNumber, $episodeNumber)
    {
        $tvShow = new TVShow();

        $tvShowEpisodeDetails = $tvShow->getTvShowEpisodeDetails($tvShowName, $seasonNumber, $episodeNumber);

        return response()->json($tvShowEpisodeDetails);
    }
}
