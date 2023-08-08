<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actor extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function seasons()
    {
        return $this->belongsToMany(Season::class, 'actor_show', 'actor_id', 'season');
    }
}
