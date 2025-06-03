@extends('layouts.dashboard')
@section('title', $slug)
@section('content')

    <script>
function enableEdit(id) {
    const row = document.getElementById('row-' + id);
    row.querySelectorAll('.display-mode').forEach(el => el.classList.add('d-none'));
    row.querySelectorAll('.edit-mode').forEach(el => el.classList.remove('d-none'));
}

function cancelEdit(id) {
    const row = document.getElementById('row-' + id);
    row.querySelectorAll('.edit-mode').forEach(el => el.classList.add('d-none'));
    row.querySelectorAll('.display-mode').forEach(el => el.classList.remove('d-none'));
}
</script>



    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col">
                <h3 class="mb-0 h4 fw-bold">Complimentary Badge</h3>
            </div>
        </div>
        <form method="GET" action="{{ route('allocations.list') }}" class="mb-3">
            <div class="input-group" style="max-width: 400px;">
                <input type="text" name="search" class="form-control" placeholder="Search by company name" value="{{ $search }}">
                <button class="btn btn-outline-primary" type="submit">Search</button>
            </div>
        </form>
        <div class="row">
            <div class="container mt-4">
                <div class="table-responsive">
                   <table class="table table-bordered table-sm align-middle">
    <thead class="table-primary text-center">
        <tr>
            <th>Company Name</th>
            @foreach($ticketTypes as $id => $type)
                <th>{{ $type }}</th>
            @endforeach
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($applications as $app)
            <tr id="row-{{ $app->id }}">
                <td class="fw-semibold">{{ $app->company_name }}</td>

                @foreach($ticketTypes as $id => $type)
                    <td class="text-center">
                        <span class="display-mode">{{ $app->badges[$id] ?? 0 }}</span>
                        <span class="edit-mode d-none">
                            <input type="number" name="badge_allocations[{{ $id }}]"
                                value="{{ $app->badges[$id] ?? 0 }}"
                                min="0" class="form-control form-control-sm" style="width: 80px;" />
                        </span>
                        <br>
                        <small class="text-muted">{{ $app->used[$id] ?? 0 }} used</small>
                    </td>
                @endforeach

                <td class="text-end">
                    <!-- Display Mode Buttons -->
                    <div class="display-mode">
                        <button class="btn btn-sm btn-outline-warning"
                            onclick="enableEdit({{ $app->id }})">Edit</button>
                        <a href="{{ route('allocations.read', $app->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                    </div>

                    <!-- Edit Mode Buttons -->
                    <form method="POST" action="{{ route('allocations.update', $app->id) }}" class="edit-mode d-none">
                        @csrf
                        @method('POST')
                        <button type="submit" class="btn btn-sm btn-outline-success">Save</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="cancelEdit({{ $app->id }})">Cancel</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<div class="d-flex justify-content-between align-items-center mt-3">
    <div>
        Showing {{ $applications->firstItem() }} to {{ $applications->lastItem() }} of {{ $applications->total() }} entries
    </div>
    <div>
        {{ $applications->withQueryString()->links() }}
    </div>
</div>



                </div>
            </div>
        </div>
    </div>


@endsection
