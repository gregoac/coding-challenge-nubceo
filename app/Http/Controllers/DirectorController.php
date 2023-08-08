<?php

namespace App\Http\Controllers;

use App\Models\Director;
use Illuminate\Http\Request;

class DirectorController extends Controller
{
     public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $director = Director::create([
            'name' => $request->name,
        ]);

        return response()->json(['message' => 'Director created successfully', 'director' => $director]);
    }
}
