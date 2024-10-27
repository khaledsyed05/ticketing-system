@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edit Ticket</div>

                <div class="card-body">
                    <form action="{{ route('tickets.update', $ticket) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                id="title" name="title" value="{{ old('title', $ticket->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                id="description" name="description" rows="3" required>{{ old('description', $ticket->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="status_id" class="form-label">Status</label>
                            <select class="form-select @error('status_id') is-invalid @enderror" 
                                id="status_id" name="status_id" required>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->id }}" 
                                        {{ old('status_id', $ticket->status_id) == $status->id ? 'selected' : '' }}>
                                        {{ $status->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('status_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="assigned_user_id" class="form-label">Assign To</label>
                            <select class="form-select @error('assigned_user_id') is-invalid @enderror" 
                                id="assigned_user_id" name="assigned_user_id" required>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" 
                                        {{ old('assigned_user_id', $ticket->assigned_user_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="deadline" class="form-label">Deadline</label>
                            <input type="date" class="form-control @error('deadline') is-invalid @enderror" 
                                id="deadline" name="deadline" 
                                value="{{ old('deadline', $ticket->deadline->format('Y-m-d')) }}" required>
                            @error('deadline')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Update Ticket</button>
                            <a href="{{ route('tickets.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection