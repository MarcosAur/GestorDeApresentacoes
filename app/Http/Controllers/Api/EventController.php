<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\EventRequest;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('all')) {
            return response()->json(Event::latest()->get());
        }
        return response()->json(Event::latest()->paginate(10));
    }

    public function store(EventRequest $request)
    {
        $event = Event::create(array_merge($request->validated(), [
            'admin_id' => Auth::id(),
        ]));

        return response()->json($event, 201);
    }

    public function show(Event $event)
    {
        return response()->json($event);
    }

    public function update(EventRequest $request, Event $event)
    {
        $event->update($request->validated());

        return response()->json($event);
    }

    public function destroy(Event $event)
    {
        if ($event->contests()->exists()) {
            return response()->json([
                'message' => 'Não é possível deletar um evento que possui concursos associados.'
            ], 422);
        }

        $event->delete();

        return response()->json(['message' => 'Evento deletado com sucesso!']);
    }
}
