<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Filter;

class FilterController extends Controller
{
    public function create()
    {
        return view('filters.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_email' => 'required|email',
                'site' => 'required|in:olx,otomoto',
                'category' => 'required|string|max:255',
                'search_text' => 'nullable|string|max:255',
                'price_from' => 'nullable|numeric',
                'price_to' => 'nullable|numeric',
                'year_from' => 'nullable|integer',
                'year_to' => 'nullable|integer',
            ]);

            Filter::create($validated);

            return redirect()->route('filters.create')->with('success', 'Filtr zapisany!');
        } catch (\Exception $e) {
            \Log::error('Error storing filter: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'input' => $request->all(),
            ]);

            return redirect()->route('filters.create')->with('error', 'Wystąpił błąd podczas zapisywania filtra.');
        }
    }

    public function showForm()
    {
        return view('filters.index');
    }

    public function listFilters(Request $request)
    {
        $email = $request->input('email');
        $filters = Filter::where('user_email', $email)->get();

        return view('filters.index', compact('filters', 'email'));
    }

    public function deleteFilter($id)
    {
        $filter = Filter::findOrFail($id);
        $filter->delete();

        return response()->json(['message' => 'Filtr usunięty']);
    }
}
