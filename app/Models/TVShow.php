<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Season;
use App\Models\Episode;
use App\Models\Director;
use App\Models\Actor;
use Illuminate\Support\Facades\DB;

class TVShow extends Model
{
    use HasFactory;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tv_shows';
    protected $fillable = ['name', 'director_id'];

    public function director()
    {
        return $this->belongsTo(Director::class);
    }

    public function seasons()
    {
        return $this->hasMany(Season::class, 'tv_show_id');
    }

    public static function createTvShowWithSeasonsAndEpisodes($data)
    {
        // transaction for consistency
        DB::beginTransaction();

        try {
            $director = Director::firstOrCreate(['name' => $data['director_name']]);
            
            $showData = [
                'name' => $data['name'],
                'director_id' => $director->id,
            ];

            $tvShow = self::create($showData);

            foreach ($data['seasons'] as $seasonData) {
            $season = new Season([
                'season_number' => $seasonData['season_number'],
                'tv_show_id' => $tvShow->id,
            ]);
            $season->save();
    
            foreach ($seasonData['episodes'] as $episodeData) 
            {
                $episode = new Episode([
                    'name' => $episodeData['name'],
                    'episode_number' => $episodeData['episode_number'],
                    'season_id' => $season->id,
                ]);
                $episode->save();
            }
    
            if (isset($seasonData['actors'])) 
            {
                foreach ($seasonData['actors'] as $actorName) 
                {
                    $actor = Actor::firstOrCreate(['name' => $actorName]);
                    DB::table('actor_show')->insert([
                        'actor_id' => $actor->id,
                        'tv_show_id' => $tvShow->id,
                        'season_id' => $season->id,
                    ]);
                }
            }
            }

            DB::commit();

            return $tvShow;
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    public function getTvShowEpisodeDetails($tvShowName, $seasonNumber, $episodeNumber)
    {

        \DB::listen(function ($query) {
            \Log::info($query->sql, $query->bindings);
        });

        $tvShowName = ucwords(str_replace('-', ' ', $tvShowName));

        $tvShow = $this->where('name', $tvShowName)->firstOrFail();

        $season = Season::where('season_number', $seasonNumber)
            ->where('tv_show_id', $tvShow->id)
            ->firstOrFail();

        $episode = Episode::where('episode_number', $episodeNumber)
            ->where('season_id', $season->id)
            ->firstOrFail();

        $tvShow->load([
            'director' => function($query){
                $query->select('id','name');
            },
            'seasons' => function ($query) use ($season) {
                $query->where('id', $season->id);
            },
            'seasons.episodes' => function ($query) use ($episode) {
                $query->where('id', $episode->id);
            },
            'seasons.actors',
        ]);

        return $tvShow;
    }

}
