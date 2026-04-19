<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\JurorRequest;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class JurorController extends Controller
{
    public function index(Request $request)
    {
        $query = User::whereHas('role', function($q) { 
            $q->where('slug', 'jurado'); 
        })->with('contests')->latest();

        if ($request->has('all')) {
            return response()->json($query->get());
        }

        return response()->json($query->paginate(10));
    }

    public function store(JurorRequest $request)
    {
        $jurorRole = Role::where('slug', 'jurado')->firstOrFail();

        $juror = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $jurorRole->id,
        ]);

        $juror->contests()->sync($request->selectedContests ?? []);

        return response()->json($juror->load('contests'), 201);
    }

    public function show(User $juror)
    {
        return response()->json($juror->load('contests'));
    }

    public function update(JurorRequest $request, User $juror)
    {
        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $juror->update($data);
        $juror->contests()->sync($request->selectedContests ?? []);

        return response()->json($juror->load('contests'));
    }

    public function destroy(User $juror)
    {
        $juror->delete();
        return response()->json(['message' => 'Jurado removido com sucesso!']);
    }
}
