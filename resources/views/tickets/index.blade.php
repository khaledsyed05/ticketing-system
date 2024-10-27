@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-12">
            <h2>Tickets</h2>
            <a href="{{ route('tickets.create') }}" class="btn btn-primary">Create New Ticket</a>
        </div>
    </div>

    <div class="row">
        @foreach($statuses as $status)
        <div class="col">
            <div class="card">
                <div class="card-header bg-{{ $status->color }}">
                    <h5 class="card-title mb-0 text-dark">{{ $status->name }}</h5>
                </div>
                <div class="card-body ticket-column">
                    @foreach($tickets->where('status_id', $status->id) as $ticket)
                    <div class="card mb-2 ticket-card">
                        <div class="card-body">
                            <h6 class="card-title">{{ $ticket->title }}</h6>
                            <p class="card-text small text-muted mb-1">
                                Created by: {{ $ticket->user->name }}
                            </p>
                            <p class="card-text small text-muted mb-1">
                                Assigned to: {{ $ticket->assignedUser->name }}
                            </p>
                            <p class="card-text small text-muted mb-2">
                                Deadline: {{ $ticket->deadline->format('Y-m-d') }}
                            </p>
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('tickets.show', $ticket) }}" 
                                   class="btn btn-info">View</a>
                                <a href="{{ route('tickets.edit', $ticket) }}" 
                                   class="btn btn-warning">Edit</a>
                                <form action="{{ route('tickets.destroy', $ticket) }}" 
                                      method="POST" 
                                      style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-danger" 
                                            onclick="return confirm('Are you sure?')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
.ticket-column {
    max-height: calc(100vh - 200px);
    overflow-y: auto;
}

.ticket-card {
    border-left: 3px solid #6c757d;
    transition: all 0.3s ease;
}

.ticket-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.card-header {
    padding: 0.75rem 1.25rem;
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
</style>
@endsection