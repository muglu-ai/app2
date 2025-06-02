@extends('layouts.users')
@section('title', 'Dashboard')
@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col">
                <h3 class="mb-0 h4 font-weight-bolder">Dashboard</h3>
            </div>
{{--            <div class="col text-end">--}}
{{--                @if($application->submission_status == 'approved')--}}
{{--                    <button class="btn btn-primary" onclick="showCoExhibitorForm()">Add Co-Exhibitor</button>--}}
{{--                @endif--}}
{{--            </div>--}}
        </div>
        <div class="row">
            {{-- @dd($badgeAllocations) --}}
        @foreach($badgeAllocations as $badge)
        @php
            $passName = '';
            // If {{ $badge['ticket_type'] }} does not contain Pass or Ticket, Then add 'Pass' to it in the last.
            if (str_contains($badge['ticket_type'], 'Pass') || str_contains($badge['ticket_type'], 'Ticket')) {
                $passName = $badge['ticket_type'];
            } else {
                $passName = $badge['ticket_type'] . ' Pass';
            }
        @endphp
            <div class="col-xl-3 col-sm-6 mb-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-sm mb-0 text-capitalize">Total {{$passName}}  Allocated</p>
                            <h4 class="mb-0">{{ $badge['badge_count'] }}</h4>
                        </div>
                        <div class="icon icon-md icon-shape bg-gradient-dark text-center border-radius-lg">
                            <i class="material-symbols-rounded opacity-10">weekend</i>
                        </div>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <p class="mb-0 text-sm">
                            <a href="{{ route('exhibition.list', ['type' => $badge['ticket_type']]) }}"> <span class="text-success font-weight-bolder"> Click here </span> </a>for more info.
                        </p>

                    </div>
                </div>
            </div>
        @endforeach
    </div>

    </div>

    <div id="coExhibitorModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Co-Exhibitor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="coExhibitorForm">
                        @csrf
                        <input type="hidden" name="application_id" value="{{ $application->id }}">

                        <div class="mb-3">
                            <label for="co_exhibitor_name" class="form-label">Co-Exhibitor Company Name</label>
                            <input type="text" class="form-control" id="co_exhibitor_name" name="co_exhibitor_name" required>
                        </div>

                        <div class="mb-3">
                            <label for="contact_person" class="form-label">Contact Person</label>
                            <input type="text" class="form-control" id="contact_person" name="contact_person" required>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>

                        <button type="button" class="btn btn-primary" onclick="submitCoExhibitor()">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        function showCoExhibitorForm() {
            $('#coExhibitorModal').modal('show');
        }

        function submitCoExhibitor() {
            let formData = $("#coExhibitorForm").serialize();

            $.ajax({
                url: "{{ route('co_exhibitor.store') }}",
                type: "POST",
                data: formData,
                success: function(response) {
                    console.log(response);
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Co-Exhibitor request submitted for approval!',
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    let errorMessage = 'Something went wrong. Please try again!';
                    if (xhr.status === 400) {
                        errorMessage = xhr.responseJSON.error || 'Bad Request. Please check your input.';
                    } else if (xhr.status === 401) {
                        errorMessage = 'Unauthorized. Please log in.';
                    } else if (xhr.status === 403) {
                        errorMessage = 'Forbidden. You do not have permission.';
                    } else if (xhr.status === 404) {
                        errorMessage = 'Not Found. The requested resource could not be found.';
                    } else if (xhr.status === 500) {
                        errorMessage = xhr.responseJSON.error || 'Internal Server Error. Please try again later.';
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: errorMessage,
                    });
                }
            });
        }
    </script>
@endsection
