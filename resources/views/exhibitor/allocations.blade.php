@extends('layouts.dashboard')
@section('title', $slug)
@section('content')

 <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col">
                <h3 class="mb-0 h4 font-weight-bolder">Complimentary Badge </h3>
            </div>
            </div>
        <div class="row">
           <div class="container mt-4">
    <div class="alert alert-danger text-white fw-bold">
        Company: {{ $companyName ?? 'N/A' }}
    </div>

    <div class="card">
        <div class="card-header bg-gradient-light fw-bold">
            Complimentary Badge Allocations
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Ticket Type</th>
                            <th>Badge Count</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($badgeAllocations as $badge)
                        <tr>
                            <td>{{ $badge->ticket_type }}</td>
                            <td>{{ $badge->count }}</td>
                            <td class="text-end">
                                <a href="{{ route('badge.read', $badge->id) }}" class="btn btn-sm btn-outline-primary">Read</a>
                                <a href="{{ route('badge.edit', $badge->id) }}" class="btn btn-sm btn-outline-warning">Edit</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer bg-white">
            <form action="{{ route('badge.add') }}" method="POST" class="row g-2 align-items-center">
                @csrf
                <div class="col-md-5">
                    <select name="ticket_type" class="form-select" required>
                        <option value="">Select Category</option>
                        @foreach($availableCategories as $category)
                        <option value="{{ $category }}">{{ $category }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" name="badge_count" class="form-control" placeholder="Badge Count" required min="1">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-success w-100" type="submit">Add</button>
                </div>
            </form>
        </div>
    </div>
</div>

        </div>
    </div>
@endsection
