@extends('layouts.dashboard')
@section('title', 'Extra Requirement Orders')

@section('content')
<style>
    th {
    text-align: left !important;
    padding-left:10px !important;
    }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- CSRF Token Meta -->
    <script>
        function openModal(paymentId) {
            var modal = new bootstrap.Modal(document.getElementById('receiptModal' + paymentId));
            modal.show();
        }

        function verifyPayment(paymentId, status, user) {
            let remarks = document.getElementById('remarks' + paymentId).value;

            if (!remarks.trim()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Remarks Required',
                    text: 'Please provide remarks before verifying the payment.',
                });
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: `You are about to mark this payment as ${status.toUpperCase()}.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, confirm!',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Proceed with AJAX request
                    $.ajax({
                        url: '/verify-extra-payment',
                        type: 'POST',
                        data: {
                            payment_id: paymentId,
                            status: status,
                            remarks: remarks,
                            user: user,
                            _token: $('meta[name="csrf-token"]').attr('content') // CSRF token for Laravel
                        },
                        beforeSend: function () {
                            Swal.fire({
                                title: 'Processing...',
                                text: 'Please wait while we verify the payment.',
                                allowOutsideClick: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        },
                        success: function (response) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message || 'Payment verified successfully.',
                            }).then(() => {
                                location.reload(); // Reload page to reflect changes
                            });
                        },
                        error: function (xhr) {
                            let errorMessage = xhr.responseJSON?.message || 'An error occurred while verifying the payment.';
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: errorMessage,
                            });
                        }
                    });
                }
            });
        }
    </script>

    <div class="container-fluid">
        <h2 class="mb-4">@yield('title')</h2>

        @if($orders->isEmpty())
            <p>No orders have been placed yet.</p>
        @else
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header pb-0">

                            <div class="table-responsive">
                                <table class="table table-flush  coe" id="products-list">
                                    <thead class="thead-light table-dark">
                                    <tr>
                        <th class=" text-uppercase"   >Invoice No</th>
                        <th class=" text-uppercase">Company</th>
                        <th class=" text-uppercase">Email</th>
                        <th class=" text-uppercase" style="padding-left:20px !important;">Order Items</th>
                        <th class=" text-uppercase" style="padding-left:4px !important;">Total Amount</th>
                        <th class=" text-uppercase" style="padding-left:4px !important;">Payment Status</th>
                        <th class=" text-uppercase" style="padding-left:10px !important;">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($orders as $order)
                        @php
                            $billingDetails = $order->application_id ? \App\Models\Application::find($order->application_id)->billingDetail : null;
                            $payments = \App\Models\Payment::where('invoice_id', $order->invoice_id)->get();
                        @endphp

                        @if($billingDetails !=null)
                            @php
                                $company = $billingDetails->billing_company;
                                $email = $billingDetails->email;
                            @endphp
                        @endif

                        <tr>
                            <td class="text-dark text-md">{{ $order->invoice->invoice_no ?? 'N/A' }}</td>
                            <td class="text-dark text-md">{{ $company ?? 'N/A' }}</td>
                            <td class="text-dark text-md">{{ $email ?? $order->user->email }}</td>
                            <td class="text-dark text-md">
                                <ul class="text-dark text-md">
                                    @foreach ($order->orderItems as $item)
                                        <li class="text-dark text-md">
                                            {{ $item->requirement->item_name }} ({{ $item->quantity }} units)
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-dark text-md">₹{{ number_format($order->invoice->amount ?? 0, 2) }}</td>
                            <td class="text-dark text-md"><span class=" badge d-block w-55 bg-{{$order->invoice->payment_status == 'paid' ? 'success' : 'danger' }}">{{ ucfirst($order->invoice->payment_status ?? 'N/A') }}</span></td>
                            <td>
                                @foreach($payments as $payment)
                                    <button class="btn btn-info" onclick="openModal({{ $payment->id }})">View Payment {{ $loop->iteration }}</button>
                                @endforeach
                            </td>
                        </tr>

                        @foreach($payments as $payment)
                            <div class="modal fade" id="receiptModal{{ $payment->id }}" tabindex="-1" aria-labelledby="receiptModalLabel{{ $payment->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="receiptModalLabel{{ $payment->id }}">Receipt Image</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>

                                        <div class="modal-body">
                                            @if($payment->receipt_image)
                                                <img src="{{ asset('storage/' . $payment->receipt_image) }}" alt="Receipt Image" class="img-fluid">
                                            @else
                                                <p class="text-muted">No receipt available</p>
                                            @endif

                                            <div class="mt-3 input-group input-group-dynamic">
                                                <textarea id="remarks{{ $payment->id }}" name="remarks" class="form-control" placeholder="Enter remarks for payment {{ $payment->id }}" required></textarea>
                                            </div>
                                            <div class="mt-3 text-center">
                                                <button type="button" class="btn btn-success" onclick="verifyPayment({{ $payment->id }}, 'verified', '{{ Auth::user()->name }}')">Verify</button>
                                                <button type="button" class="btn btn-danger" onclick="verifyPayment({{ $payment->id }}, 'rejected', '{{ Auth::user()->name }}')">Reject</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
