<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function jurors()
    {
        $jurors = User::whereHas('role', function($q) { 
            $q->where('slug', 'jurado'); 
        })->orderBy('name')->get();

        return response()->json($jurors);
    }
}
