@extends('layouts.dashboard_side')
@section('content')
    <div class="container-fluid">
    <div class="text-center">
        <div class="center align-content-center pt-2">
            <h4>Exhibition Events</h4>
        </div>
        <div class="row justify-content-center m-lg-3">
            @foreach($events as $event)
                @if($event->event_name != 'SEMICON INDIA')
                <div class="col-lg-4 col-md-4 mx-3 mb-4">
                    <div class="card text-center" data-animation="false">
                        <!-- Image Section -->
                        <div class="card-header p-2 position-relative z-index-2 bg-transparent">
                            <a class="d-block blur-shadow-image">
                                <img src="{{ $event->event_image }}" alt="img-blur-shadow" class="img-fluid shadow border-radius-lg" style="max-width: 100%; height: auto;">
                            </a>
                        </div>

                        <!-- Event Name, Location, and Button in One Row -->
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <!-- Event Name on Left -->
                            <h5 class="font-weight-bold mt-3 text-start">
                                <a href="javascript:;" class="d-block">{{ $event->event_name }}</a>
                            </h5>

                            <!-- Location in Center -->
                            <div class="d-flex align-items-center text-dark">
                                <i class="material-symbols-rounded text-lg me-1">place</i>
                                <p class="text-sm my-auto">{{ $event->event_location }}</p>
                            </div>

                        </div>
                        <div class="d-flex justify-content-end align-items-center mt-2 gap-3">
                            @php
                                try {
                                    $userId = Auth::id();
                                    $eventId = $event->id;

                                    // Fetch the application with conditions
                                    $dashboard = App\Models\Application::hasApplication($userId, $eventId)
                                    ->whereIn('submission_status', ['approved', 'submitted'])
                                    ->whereHas('invoice', function($query) {
                                        $query->where('type', 'Stall Booking')->where('payment_status', 'paid');
                                    })->first();

//                                     $query = App\Models\Application::hasApplication($userId, $eventId)
//     ->whereIn('submission_status', ['approved', 'submitted'])
//     ->whereHas('invoice', function($query) {
//         $query->where('type', 'Stall Booking')->where('payment_status', 'paid');
//     });

// dd($query->toSql(), $query->getBindings(), $query->get());

                                    // Check if any application exists
                                    $applicationExists = App\Models\Application::hasApplication($userId, $eventId)->exists();

                                    // Check if application is approved
                                    $isApproved = App\Models\Application::hasApplication($userId, $eventId)
                                        ->where('submission_status', 'approved')->exists();

                                    // Set button attributes
                                    $buttonClass = $applicationExists ? 'btn-success' : 'bg-gradient-dark';
                                    $buttonText = $applicationExists ? 'Continue booking' : 'Apply';
                                    $buttonLink = $isApproved ? '/preview' : "/{$event->slug}/onboarding";
                                } catch (\Exception $e) {
                                    $dashboard = null;
                                }
                            @endphp

                            <div class="d-flex justify-content-between w-100">
                                @if ($dashboard)
                                    <a href="{{ route('user.dashboard') }}" class="btn btn-primary ms-3">
                                        Go to Dashboard
                                    </a>
                                @endif

                                <a href="{{ $buttonLink }}" class="btn {{ $buttonClass }} me-3 ms-3">
                                    <i class="material-symbols-rounded text-white text-lg "></i>{{ $buttonText }}
                                </a>
                            </div>
                        </div>
                    </div>
                    </div>
                   </div>
                   </div>
                    </div>
@endif
            @endforeach

    @endsection


















