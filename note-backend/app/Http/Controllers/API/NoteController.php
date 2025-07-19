<?php

namespace App\Http\Controllers\API;

use App\Models\Note;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NoteController extends Controller
{
    public function index(Request $request)
    {
        $query = Note::query();

        // Filter by type if provided
        if ($request->has('type')) {
            $type = $request->input('type');
            if (in_array($type, ['task', 'note'])) {
                $query->where('type', $type);
            }
        }

        // Filter by completion status if provided
        if ($request->has('isdone')) {
            $isdone = filter_var($request->input('isdone'), FILTER_VALIDATE_BOOLEAN);
            $query->where('isdone', $isdone);
        }

        // Search functionality
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('body', 'like', "%{$searchTerm}%")
                  ->orWhere('category', 'like', "%{$searchTerm}%");
            });
        }

        return response()->json($query->latest()->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'type' => Note::getTypeValidationRule(),
            'isdone' => 'sometimes|boolean',
        ]);

        // Set default value if not provided
        if (!isset($validated['isdone'])) {
            $validated['isdone'] = false;
        }

        $note = Note::create($validated);
        return response()->json([
            'message' => 'Item created successfully',
            'data' => $note
        ], 201);
    }

    public function show($id)
    {
        $note = Note::findOrFail($id);
        return response()->json($note);
    }

    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'body' => 'nullable|string',
                'category' => 'nullable|string|max:100',
                'type' => Note::getTypeValidationRule(),
                'isdone' => 'sometimes|boolean',
            ]);

            $note = Note::findOrFail($id);
            $note->update($validated);
            
            return response()->json([
                'message' => 'Item updated successfully',
                'data' => $note
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Item not found'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating the item',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        $note = Note::findOrFail($id);
        $note->delete();
        return response()->json([
            'message' => 'Item deleted successfully'
        ], 200);
    }

    // New method to get only tasks
    public function tasks(Request $request)
    {
        $query = Note::tasks();

        if ($request->has('isdone')) {
            $isdone = filter_var($request->input('isdone'), FILTER_VALIDATE_BOOLEAN);
            $query->where('isdone', $isdone);
        }

        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('body', 'like', "%{$searchTerm}%")
                  ->orWhere('category', 'like', "%{$searchTerm}%");
            });
        }

        return response()->json($query->latest()->get());
    }

    // New method to get only notes
    public function notes(Request $request)
    {
        $query = Note::notes();

        if ($request->has('isdone')) {
            $isdone = filter_var($request->input('isdone'), FILTER_VALIDATE_BOOLEAN);
            $query->where('isdone', $isdone);
        }

        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('body', 'like', "%{$searchTerm}%")
                  ->orWhere('category', 'like', "%{$searchTerm}%");
            });
        }

        return response()->json($query->latest()->get());
    }

    // New method to toggle completion status
    public function toggleDone($id)
    {
        $note = Note::findOrFail($id);
        $note->update(['isdone' => !$note->isdone]);
        
        return response()->json([
            'message' => 'Completion status toggled successfully',
            'data' => $note
        ]);
    }
}