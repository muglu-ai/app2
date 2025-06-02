@extends('layouts.dashboard')

@section('content')
    <h2 class="text-xl font-bold mb-4">Organizations</h2>

    <!-- Unified Filter + Search + Download Form -->
    <form method="GET" class="mb-4 flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-sm font-medium">Payment Status</label>
            <select name="pay_status" class="border rounded p-1">
                <option value="">-- All --</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" {{ request('pay_status') == $status ? 'selected' : '' }}>
                        {{ $status }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium">Sector</label>
            <select name="sector" class="border rounded p-1">
                <option value="">-- All --</option>
                @foreach($sectors as $sector)
                    <option value="{{ $sector }}" {{ request('sector') == $sector ? 'selected' : '' }}>
                        {{ $sector }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium">Search (TIN / Org Name)</label>
            <input type="text" name="search" value="{{ request('search') }}" class="border p-1 rounded w-full" />
        </div>

        <div class="flex gap-2">
            <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded">Apply</button>

            <a href="{{ route('organizations.export', request()->query()) }}"
                class="bg-green-600 text-black px-3 py-1 rounded">
                Download
            </a>
        </div>
    </form>


    <!-- Table -->
    <table class="table-auto w-full text-sm border border-gray-300">
        <thead class="bg-gray-100">
            <tr>
                <th class="p-2">Sl No</th>
                <th class="p-2">Org Name</th>
                <th class="p-2">TIN No</th>
                <th class="p-2">Reg Date</th>
                <th class="p-2">Sector</th>
                <th class="p-2">Payment Status</th>
                <th class="p-2">Amount</th>
                <th class="p-2">Total Delegates</th>
                <th class="p-2">Actions</th>
            </tr>
        </thead>

        <tbody>
            @forelse($orgs as $index => $org)
                <tr class="border-t">
                    <td class="p-2">{{ ($orgs->currentPage() - 1) * $orgs->perPage() + $index + 1 }}</td>
                    <td class="p-2">{{ $org->org_name }}</td>
                    <td class="p-2">{{ $org->tin_no }}</td>
                    <td class="p-2">{{ \Carbon\Carbon::parse($org->reg_date)->format('d M Y') }}</td>
                    <td class="p-2">{{ $org->sector }}</td>
                    <td class="p-2">{{ $org->pay_status }}</td>
                    <td class="p-2">₹{{ number_format($org->total, 2) }}</td>
                    <td class="p-2">{{ $org->delegates()->count() }}</td>
                    <td class="p-2">
                        <a href="{{ route('organizations.show', $org->id) }}" class="text-blue-500">View</a>
                        <!-- | <a href="{{ route('organizations.delegates', $org->id) }}" class="text-green-500">Delegates</a> -->
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center p-4 text-gray-500">No organizations found.</td>
                </tr>
            @endforelse
        </tbody>

    </table>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $orgs->links() }}
    </div>
@endsection