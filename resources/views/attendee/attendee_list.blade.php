@extends('layouts.dashboard')
@section('title', ucfirst($slug))
@section('content')

<style>
    .custom-header {
        background-color: #343a40; /* Dark header */
        color: #fff;
        
    }
    .custom-header a {
        color: #fff !important;
    }
    th, td {
        vertical-align: middle !important;
        padding: 12px 16px !important;
    }
    .table-hover tbody tr:hover {
        background-color: #f4f6f9;
        cursor: pointer;
    }
    .card-custom {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
    }
    .search-bar {
        border-radius: 0.5rem;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    #pagination {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-top: 20px;
}
</style>
<style>
    /* Laravel pagination container */
    .pagination nav {
        background-color: #eef6fb;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        margin-top: 20px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        font-family: 'Segoe UI', sans-serif;
        gap: 16px;
        text-align: center;
    }
    
    /* Center the "Showing X to Y of Z results" text */
    .pagination nav > div:first-child {
        width: 100%;
        display: flex;
        justify-content: center;
    }
    
    .pagination nav > div:first-child > p {
        font-size: 14px;
        color: #374151;
        margin: 0;
    }
    
    /* Center the page number buttons */
    .pagination nav > div:last-child > span {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 6px;
    }
    
    /* Page button styling */
    .pagination nav .page-link,
    .pagination nav .page-link:focus {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 36px;
        height: 36px;
        padding: 0 12px;
        font-size: 14px;
        font-weight: 500;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        background-color: #ffffff;
        color: #374151;
        transition: all 0.2s ease-in-out;
        text-decoration: none;
    }
    
    /* Active page button */
    .pagination nav .page-item.active .page-link {
        background-color: #0d6efd;
        color: white;
        border-color: #0d6efd;
    }
    
    /* Hover effect */
    .pagination nav .page-link:hover {
        background-color: #e0f2fe;
        color: #0c4a6e;
        border-color: #60a5fa;
    }
    
    /* Disabled arrows */
    .pagination nav .page-item.disabled .page-link {
        background-color: #f3f4f6;
        color: #9ca3af;
        cursor: not-allowed;
    }
    
    /* Responsive layout fix */
    @media (max-width: 576px) {
        .pagination nav {
            padding: 16px;
            gap: 12px;
        }
    
        .pagination nav .page-link {
            font-size: 13px;
            min-width: 34px;
            height: 34px;
            padding: 0 10px;
        }
    }
    </style>
    
    
<div class="container-fluid py-3">
    <div class="row">
        <div class="col-12">

            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-semibold mb-0">🧾 Attendee List</h4>
            </div>

            <!-- Search Bar -->
            {{-- <form method="GET" action="{{ route('visitor.list') }}" class="mb-4">
                <div class="input-group search-bar">
                    <input type="text" name="search" class="form-control" placeholder="🔍 Search name, email, company, or ID..." value="{{ request('search') }}">
                    <button class="btn btn-outline-primary px-4" type="submit">Search</button>
                </div>
            </form> --}}

            <!-- Attendees Table -->
            <div class="card card-custom">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="datatable-basic">
                            <div class="d-flex justify-content-end mb-3">
                                <a href="{{ route('export.list') }}" class="btn btn-success">
                                    <i class="fas fa-download"></i> Export Attendees
                                </a>
                            </div>
                            <thead class="custom-header">
                                <tr>
                                    <th class="custom-header">#</th>
                                    <th class="custom-header">Unique ID</th>
                                    <th class="custom-header">Contact Person</th>
                                    <th class="custom-header">Company & Designation</th>
                                    {{-- <th class="custom-header">Status</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($attendees as $index => $attendee)
                                <tr>
                                    <td>{{ $attendees->firstItem() + $index }}</td>
                                    <td>{{ $attendee->unique_id }}</td>
                                    <td>
                                        <div>{{ $attendee->first_name }} {{ $attendee->last_name }}</div>
                                        <div class="text-muted">{{ $attendee->email }}</div>
                                        <div class="text-muted">{{ $attendee->mobile }}</div>
                                    </td>
                                    <td><div>{{ $attendee->company }} </div>
                                        <div class="text-muted">{{ $attendee->designation }}</div>
                                    </td>
                                    {{-- <td>
                                        <span class="badge 
                                            @if($attendee->status == 'Active') bg-success
                                            @elseif($attendee->status == 'pending') bg-warning text-dark
                                            @else bg-secondary
                                            @endif">
                                            {{ ucfirst($attendee->status) }}
                                        </span>
                                    </td> --}}
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No attendees found.</td>
                                </tr>
                                @endforelse
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4 pagination">
                {{ $attendees->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endsection
