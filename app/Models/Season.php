<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    use HasFactory;
    protected $fillable = ['season_number', 'tv_show_id'];

    public function tvShows()
    {
        return $this->belongsToMany(TVShow::class, 'actor_show')->withPivot('season_id');
    }

    public function episodes()
    {
        return $this->hasMany(Episode::class);
    }

    public function actors()
    {
        return $this->belongsToMany(Actor::class, 'actor_show', 'season_id', 'actor_id');
    }

}
