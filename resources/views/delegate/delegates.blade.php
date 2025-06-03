@extends('layouts.dashboard')

@section('content')
    <h2 class="text-xl font-bold mb-4">Delegates for {{ $organization->org_name }}</h2>

    <table class="min-w-full bg-white shadow rounded-lg">
        <thead>
            <tr class="border-b">
                <th class="p-3 text-left">Name</th>
                <th class="p-3 text-left">Email</th>
                <th class="p-3">Designation</th>
            </tr>
        </thead>
        <tbody>
            @foreach($delegates as $delegate)
                <tr class="border-b hover:bg-gray-50">
                    <td class="p-3">{{ $delegate->title }} {{ $delegate->first_name }} {{ $delegate->last_name }}</td>
                    <td class="p-3">{{ $delegate->email }}</td>
                    <td class="p-3">{{ $delegate->designation }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-4">
        <a href="{{ route('organizations.index') }}" class="text-blue-600">← Back to List</a>
    </div>
@endsection