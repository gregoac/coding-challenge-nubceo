<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Actor;

class Movie extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'director_id'];

    public function director()
    {
        return $this->belongsTo(Director::class);
    }

    public function actors()
    {
        return $this->belongsToMany(Actor::class);
    }

    public function attachActorsByNames(array $actorNames)
    {
        $actorIds = collect($actorNames)->map(function ($actorName) {
            $actorName = ucwords(strtolower($actorName));  // convert to title case
            $actor = Actor::firstOrCreate(['name' => $actorName]);
            return $actor->id;
        })->all();

        $this->actors()->sync($actorIds);
    }

    public function getDirectorByName($directorName)
    {
        $directorName = ucwords(strtolower($directorName));  // convert to title case
        $director = Director::firstOrCreate(['name' => $directorName]);

        return $director->id;
    }

    public function setAttributes($name, $directorName)
    {
        $this->name = $name;
        $this->director_id = $this->getDirectorByName($directorName);
    }

    public function setRelations($actorNames)
    {
        if ($actorNames) {
            $this->attachActorsByNames($actorNames);
        }
    }

    // this function is only to be used by the Movie model, since is static
    public static function filterAndSort($name, $director, $sortField, $sortOrder)
    {
        $name = ucwords(str_replace('-', ' ', $name));
        $director = ucwords(str_replace('-', ' ', $director));

        $query = self::query();

        if ($name) {
            $query->where('name', 'LIKE', '%' . strtolower($name) . '%');
        }

        if ($director) {
            $query->whereHas('director', function ($query) use ($director) {
                $query->where('name', 'like', '%'. strtolower($director) .'%');
            });
        }

        if ($sortField) {
            $query->orderBy($sortField, $sortOrder);
        }

        return $query->get();
    }

}
