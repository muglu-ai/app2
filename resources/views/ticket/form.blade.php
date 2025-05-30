@extends('layouts.dashboard')

@section('content')
    <div class="container mt-4" style="max-width: 600px;">
        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white">
                <h4 class="mb-0">{{ isset($ticket) ? 'Edit' : 'Create' }} Ticket</h4>
            </div>
            <div class="card-body">
                <form action="{{ isset($ticket) ? route('tickets.update', $ticket->id) : route('tickets.store') }}"
                    method="POST" autocomplete="off">
                    @csrf
                    @if(isset($ticket)) @method('PUT') @endif

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ticket Type</label>
                        <input type="text" name="ticket_type" class="form-control" placeholder="Enter ticket type"
                            value="{{ old('ticket_type', $ticket->ticket_type ?? '') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nationality</label>
                        <select name="nationality" class="form-select">
                            <option value="" disabled {{ old('nationality', $ticket->nationality ?? '') == '' ? 'selected' : '' }}>Select nationality</option>
                            <option value="Indian" {{ old('nationality', $ticket->nationality ?? '') == 'Indian' ? 'selected' : '' }}>Indian</option>
                            <option value="International" {{ old('nationality', $ticket->nationality ?? '') == 'International' ? 'selected' : '' }}>International</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Early Bird Date</label>
                        <input type="date" name="early_bird_date" class="form-control"
                            value="{{ old('early_bird_date', $ticket->early_bird_date ?? '') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Early Bird Price</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" step="1" name="early_bird_price" class="form-control" placeholder="1"
                                value="{{ old('early_bird_price', $ticket->early_bird_price ?? '') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Normal Price</label>
                        <div class="input-group">
                            <span class="input-group-text">₹</span>
                            <input type="number" step="1" name="normal_price" class="form-control" placeholder="1"
                                value="{{ old('normal_price', $ticket->normal_price ?? '') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="" disabled {{ old('status', $ticket->status ?? '') == '' ? 'selected' : '' }}>
                                Select status</option>
                            <option value="1" {{ old('status', $ticket->status ?? '') == 'Active' ? 'selected' : '' }}>
                                Active</option>
                            <option value="0" {{ old('status', $ticket->status ?? '') == 'Inactive' ? 'selected' : '' }}>
                                Inactive</option>
                        </select>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="submit"
                            class="btn btn-success px-4">{{ isset($ticket) ? 'Update' : 'Create' }}</button>
                        <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection