<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Presentation;
use App\Models\Contest;
use App\Services\PresentationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PresentationController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('analyze')) {
            $presentations = Presentation::where('status', 'EM_ANALISE')->with(['competitor', 'contest.event'])->latest()->get();
        } else {
            $presentations = Auth::user()->presentations()->with('contest.event')->latest()->get();
        }
        return response()->json($presentations);
    }

    public function evaluate(Request $request, Presentation $presentation)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:APTO,INAPTO',
            'justification_inapto' => 'required_if:status,INAPTO|string|nullable',
        ]);

        PresentationService::evaluate($presentation, $validated);

        return response()->json(['message' => 'Apresentação avaliada com sucesso!', 'presentation' => $presentation]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contest_id' => 'required|exists:contests,id',
            'work_title' => 'required|string|max:255',
        ]);

        // Check if already enrolled
        $alreadyEnrolled = Presentation::where('contest_id', $validated['contest_id'])
            ->where('competitor_id', Auth::id())
            ->exists();

        if ($alreadyEnrolled) {
            return response()->json(['message' => 'Você já está inscrito neste concurso.'], 422);
        }

        $presentation = PresentationService::run($validated);

        return response()->json($presentation, 201);
    }

    public function show(Presentation $presentation)
    {
        $this->authorizeAccess($presentation);
        return response()->json($presentation->load(['contest.event', 'competitor']));
    }

    public function destroy(Presentation $presentation)
    {
        $this->authorizeAccess($presentation);
        $presentation->delete();
        return response()->json(['message' => 'Inscrição removida com sucesso!']);
    }

    protected function authorizeAccess(Presentation $presentation)
    {
        if ($presentation->competitor_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
            abort(403);
        }
    }
}
