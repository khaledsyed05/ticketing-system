<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with(['user', 'assignedUser', 'status'])->get();
        return response()->json($tickets);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status_id' => 'required|exists:statuses,id',
            'deadline' => 'required|date',
            'username' => 'required|string|max:255',
            'assigned_user_id' => 'required|exists:users,id'
        ]);

        $validated['user_id'] = Auth::user()->id;
        $ticket = Ticket::create($validated);
        
        return response()->json($ticket);
    }

    public function show(Ticket $ticket)
    {
        return response()->json($ticket->load(['user', 'assignedUser', 'status']));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status_id' => 'required|exists:statuses,id',
            'deadline' => 'required|date',
            'username' => 'required|string|max:255',
            'assigned_user_id' => 'required|exists:users,id'
        ]);

        $ticket->update($validated);
        
        return response()->json($ticket);
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        return response()->json(['message' => 'Ticket deleted']);
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'status_id' => 'required|exists:statuses,id'
        ]);

        $ticket->update($validated);
        
        return response()->json($ticket);
    }

    public function getByStatus($status_id)
    {
        $tickets = Ticket::with(['user', 'assignedUser', 'status'])
            ->where('status_id', $status_id)
            ->get();
            
        return response()->json($tickets);
    }

    public function getByUser($user_id)
    {
        $tickets = Ticket::with(['user', 'assignedUser', 'status'])
            ->where('user_id', $user_id)
            ->orWhere('assigned_user_id', $user_id)
            ->get();
            
        return response()->json($tickets);
    }  
}
