<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Journal;
use App\Services\RabbitMQPublisher;

class JournalController extends Controller
{
    // Get the list of journals for the authenticated user
    public function index()
    {
        $journals = auth()->user()->journals;
        return response()->json($journals);
    }

    // Create a new journal
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);

        $journal = Journal::createForUser(auth()->user(), $request->all());
        app(RabbitMQPublisher::class)->publishModelEvent($journal, 'created', 'journal', 'journal.created');

        return response()->json($journal, 201);
    }

    // Update an existing journal
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'content' => 'required',
        ]);

        $journal = auth()->user()->journals()->findOrFail($id);
        $journal->updateWithData($request->only('title', 'content'));

        return response()->json($journal);
    }
}
