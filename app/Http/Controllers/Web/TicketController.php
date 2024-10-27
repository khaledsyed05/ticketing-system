<?php

namespace App\Http\Controllers\web;

use App\Http\Controllers\Controller;
use App\Models\Status;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with(['user', 'assignedUser', 'status'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        $statuses = Status::orderBy('id')->get();
        
        return view('tickets.index', compact('tickets', 'statuses'));
    }
    public function create()
    {
        $users = User::all();
        $statuses = Status::orderBy('order')->get();
        return view('tickets.create', compact('users', 'statuses'));
    }

    public function store(Request $request)
    {
        // Get the authenticated user's ID
        $user_id = Auth::user()->id;
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status_id' => 'required|exists:statuses,id',
            'assigned_user_id' => 'required|exists:users,id',
            'deadline' => 'required|date|after:today',
        ]);
    
        try {
            $ticket = Ticket::create([
                ...$validated,
                'user_id' => $user_id // Add user_id to the creation array
            ]);
    
            return redirect()
                ->route('tickets.index')
                ->with('success', 'Ticket created successfully.');
    
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Error creating ticket: ' . $e->getMessage()]);
        }
    }
    public function show(Ticket $ticket)
    {
        $ticket->load(['user', 'assignedUser', 'status']);
        return view('tickets.show', compact('ticket'));
    }

    public function edit(Ticket $ticket)
    {
        $users = User::all();
        $statuses = Status::orderBy('order')->get();
        return view('tickets.edit', compact('ticket', 'users', 'statuses'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $user_id = Auth::user()->id;

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'status_id' => 'required|exists:statuses,id',
            'deadline' => 'required|date',
            'assigned_user_id' => 'required|exists:users,id'
        ]);

        $ticket->update([...$validated, 'user_id' => $user_id]);

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket updated successfully');
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();
        return redirect()->route('tickets.index')
            ->with('success', 'Ticket deleted successfully');
    }
}
