@extends('layouts.dashboard')
@section('title', 'Registrations - ' . $application->company_name)
@section('content')

<div class="container py-4">
    <h4 class="mb-4">All Registrations for <strong>{{ $application->company_name }}</strong></h4>

    @if($delegates->isEmpty())
    <div class="alert alert-warning text-center">
        <strong>No registrations have been completed yet.</strong>
    </div>
@else
    @foreach($delegates as $categoryId => $group)
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                {{ $categories[$categoryId] ?? 'Unknown Category' }} ({{ $group->count() }} delegates)
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Mobile</th>
                            <th>Job Title</th>
                            <th>Organisation</th>
                            <th>Registered On</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($group as $delegate)
                            <tr>
                                <td>{{ $delegate->first_name }} {{ $delegate->last_name }}</td>
                                <td>{{ $delegate->email }}</td>
                                <td>{{ $delegate->mobile }}</td>
                                <td>{{ $delegate->job_title }}</td>
                                <td>{{ $delegate->organisation_name }}</td>
                                <td>{{ \Carbon\Carbon::parse($delegate->created_at)->format('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
@endif

</div>

@endsection
