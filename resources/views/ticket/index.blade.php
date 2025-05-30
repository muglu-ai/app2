<!-- resources/views/tickets/index.blade.php -->
@extends('layouts.dashboard')
<style>
    .table tbody tr:last-child td {
        border-width: 1 !important;
    }
</style>
@section('content')
    <h2 class="mb-4">Ticket List</h2>
    <a href="{{ route('tickets.create') }}" class="btn btn-secondary mb-3">Add New Ticket</a>

    <table class="table table-bordered align-middle text-center" style="vertical-align: middle;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Type</th>
                <th>Nationality</th>
                <th>Early Bird Date</th>
                <th>Early Bird Price</th>
                <th>Normal Price</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tickets as $ticket)
                <tr style="border:1px solid #dee2e6;">
                    <td>{{ $ticket->id }}</td>
                    <td>{{ $ticket->ticket_type }}</td>
                    <td>{{ $ticket->nationality }}</td>
                    <td>{{ $ticket->early_bird_date }}</td>
                    <td>{{ $ticket->early_bird_price }}</td>
                    <td>{{ $ticket->normal_price }}</td>
                    <td>{{ $ticket->status }}</td>
                    <td>
                        <a href="{{ route('tickets.edit', $ticket->id) }}" class="btn btn-warning btn-sm">Edit</a>
                        <form action="{{ route('tickets.destroy', $ticket->id) }}" method="POST" style="display:inline-block;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection