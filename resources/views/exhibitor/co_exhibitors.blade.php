@extends('layouts.users')
@section('title', 'Co-Exhibitors List')
@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col">
                <h3 class="mb-0 h4 font-weight-bolder">Dashboard</h3>
            </div>
            <div class="col text-end">
                    <button class="btn btn-primary" onclick="showCoExhibitorForm()">Add Co-Exhibitor</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-flush mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-left  text-uppercase text-white text-md font-weight-bolder  ps-2">Name</th>
                            <th class="text-left  text-uppercase text-white text-md font-weight-bolder  ps-2">Contact Person</th>
                            <th class="text-left  text-uppercase text-white text-md font-weight-bolder  ps-2">Email</th>
                            <th class="text-left  text-uppercase text-white text-md font-weight-bolder  ps-2">Phone</th>
                            <th class="text-left  text-uppercase text-white text-md font-weight-bolder  ps-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    @if($coExhibitors->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center">No co-exhibitor is added</td>
                        </tr>
                    @else
                        @foreach($coExhibitors as $coExhibitor)
                            
                            <tr>
                                <td class="text-left text-dark text-md">{{ $coExhibitor->co_exhibitor_name }}</td>
                                <td class="text-left text-dark text-md">{{ $coExhibitor->contact_person }}</td>
                                <td class="text-left text-dark text-md">{{ $coExhibitor->email }}</td>
                                <td class="text-left text-dark text-md">{{ $coExhibitor->phone }}</td>
                                <td class="text-left text-dark text-md"><span class=" badge d-block w-45 bg-{{ $coExhibitor->status == 'approved' ? 'success' : 'danger'}}">{{ ucfirst($coExhibitor->status) }}</span></td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
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
