<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ContestRequest;
use App\Models\Contest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ContestController extends Controller
{
    public function index(Request $request)
    {
        $query = Contest::with(['event', 'jurors', 'evaluationCriteria'])->latest();

        if ($request->user()->hasRole('jurado') && !$request->user()->hasRole('admin')) {
            $query->whereHas('jurors', function($q) use ($request) {
                $q->where('user_id', $request->user()->id);
            });
        }

        if ($request->has('all')) {
            return response()->json($query->get());
        }

        return response()->json($query->paginate(10));
    }

    public function store(ContestRequest $request)
    {
        $this->validateTiebreaks($request->criteria);

        return DB::transaction(function () use ($request) {
            $contest = Contest::create([
                'event_id' => $request->event_id,
                'name' => $request->name,
                'status' => $request->status,
            ]);

            $contest->jurors()->sync($request->selectedJurors ?? []);

            foreach ($request->criteria as $criterionData) {
                $contest->evaluationCriteria()->create($criterionData);
            }

            return response()->json($contest->load(['event', 'jurors', 'evaluationCriteria']), 201);
        });
    }

    public function show(Contest $contest)
    {
        return response()->json($contest->load(['event', 'jurors', 'evaluationCriteria']));
    }

    public function update(ContestRequest $request, Contest $contest)
    {
        $this->validateTiebreaks($request->criteria);

        return DB::transaction(function () use ($request, $contest) {
            $contest->update([
                'event_id' => $request->event_id,
                'name' => $request->name,
                'status' => $request->status,
            ]);

            $contest->jurors()->sync($request->selectedJurors ?? []);

            $contest->evaluationCriteria()->delete();
            foreach ($request->criteria as $criterionData) {
                unset($criterionData['id'], $criterionData['contest_id'], $criterionData['created_at'], $criterionData['updated_at'], $criterionData['deleted_at']);
                $contest->evaluationCriteria()->create($criterionData);
            }

            return response()->json($contest->load(['event', 'jurors', 'evaluationCriteria']));
        });
    }

    public function destroy(Contest $contest)
    {
        $contest->delete();
        return response()->json(['message' => 'Concurso removido!']);
    }

    protected function validateTiebreaks(array $criteria)
    {
        $priorities = collect($criteria)->pluck('tiebreak_priority')->filter()->toArray();
        if (count($priorities) !== count(array_unique($priorities))) {
            throw ValidationException::withMessages([
                'criteria' => ['Não é permitido repetir a mesma prioridade de desempate em critérios diferentes.'],
            ]);
        }
    }
}
