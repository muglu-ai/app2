@extends('layouts.sponsor-application')
@section('title', 'Applicant Details')
@section('content')


    <style>
        @media (min-width: 500px) {
            .progress-bar2 {
                display: none !important;
            }
        }

        .red-label {
            color: red;
            font-weight: bold;
        }

        .custom-hr {
            border: none;
            height: 3px;
            background: #bfb8b8;
            width: 100%;
            margin: 20px auto;
        }

        #gst_india {
            /*display: flex !important;*/
            flex-wrap: wrap;
        }

        .black-label {
            color: #131313;
            font-weight: bold;
        }


        .choices .choices__list.choices__list--single .choices__item--selectable {
            margin-bottom: 0 !important;
            color: #000 !important;
        }

        .choices__item.choices__item--selectable .form-custom-control {
            background-image: linear-gradient(0deg, #e91e63 1px, rgba(156, 39, 176, 0) 0), linear-gradient(0deg, #d2d2d2 1px, hsla(0, 0%, 82%, 0) 0);
            border-radius: 0 !important;
        }

        .choice1,
        .choice1 :focus {
            background-image: linear-gradient(0deg, #e91e63 2px, rgba(156, 39, 176, 0) 0), linear-gradient(0deg, #d2d2d2 1px, hsla(0, 0%, 82%, 0) 0);
        }
    </style>
    <div class="container py-2">
        <div class="row min-vh-120 mt-5">
            <div class="col-lg-12 col-md-12 col-12 col-sm-12 m-auto">
                <div class="card mt-auto">
                    <div class="card-header p-0 position-relative mt-5 mx-3 z-index-2">
                        <div class="bg-gradient-dark shadow-dark border-radius-lg pt-4 pb-3">
                            <div class="multisteps-form__progress">
                                <button class="multisteps-form__progress-btn js-active" disabled>
                                    <span>1. Show Profile</span>
                                </button>
                                <button class="multisteps-form__progress-btn" disabled>2. Application
                                    Form
                                </button>
                                <button class="multisteps-form__progress-btn" disabled>3. Terms and
                                    Conditions
                                </button>
                                <button class="multisteps-form__progress-btn" disabled>4. Review
                                </button>
                            </div>
                            <small class="progress-bar2 d-block text-center text-white">1. Personal Info</small>
                        </div>
                    </div>

                    <div class="card-body" id="card-body" style="height: 1990px !important;">
                        <form class="multisteps-form__form" id="step1" method="POST" enctype="multipart/form-data"
                            action="{{ route('application.exhibitor.submit') }}">
                            @csrf
                            @php
                                $isDisabled =
                                    isset($application) && $application->submission_status != 'in progress'
                                        ? 'disabled'
                                        : '';
                            @endphp

                            <div class="multisteps-form__panel pt-3 border-radius-xl bg-white js-active"
                                data-animation="FadeIn">
                                <div class="multisteps-form__content">
                                    <div class="container">
                                        <div class="text-sm text-justify">
                                            <p> We request you to provide some information about your business to create
                                                a show specific profile for your participation. This show profile is
                                                limited to this specific event only.</p>
                                        </div>
                                        <div class="row mt-3 ">

                                        </div>
                                        <input type="hidden" name="event_id" value="{{ $event->id ?? '' }}">

                                        <div class="row mt-2 mb-5 ms-0">
                                            <div class="col-12 col-sm-6">
                                                <label class="form-control ms-0 red-label">Sector<span
                                                        class="red-label">*</span></label>

                                                <select class="form-select form-select-lg mb-3 text-dark dropdown"
                                                    name="sector" id="sectors-list" size="5" required
                                                    {{ $isDisabled }}>
                                                    <option value="" disabled {{ !isset($application) || empty($application->sector_id) ? 'selected' : '' }}>Select one of the sector
                                                    </option>
                                                    @foreach ($sectors as $sector)
                                                        <option value="{{ $sector->id }}"
                                                            @if(old('sector', $application->sector_id ?? '') == $sector->id) selected @endif>
                                                            {{ $sector->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <label class="form-control ms-0 red-label">Sub-Sector <span
                                                        class="red-label">*</span></label>
                                                <select class="form-select form-select-lg mb-3 text-dark dropdown"
                                                    name="sub_sector" id="subSectors-list" size="5" required
                                                    {{ $isDisabled }}>
                                                    <option value="" disabled selected>Select one of the Sub-Sector
                                                    </option>
                                                    @foreach ($subSectors as $subSector)
                                                        <option value="{{ $subSector }}"
                                                            {{ isset($application) && $application->sub_sector == $subSector ? 'selected' : '' }}>
                                                            {{ $subSector }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>


                                        <div class="row mt-5">
                                            <div class="col-12 col-sm-6 mt-3">
                                                <label class="red-label" for="region">Region <span
                                                        class="red-label">*</span></label>
                                                <div style="display: flex; flex-wrap: wrap; gap: 40px;"
                                                    class="form-check is-filled">
                                                    @foreach (['India', 'International'] as $region)
                                                        <div style="display: flex; align-items: center;">
                                                            <input class="form-check-input" type="radio" name="region"
                                                                value="{{ $region }}" id="region_{{ $loop->index }}"
                                                                {{ $isDisabled }}
                                                                {{ old('region', isset($application) ? $application->region : null) == $region || (!isset($application) && $region == 'India') ? 'checked' : '' }}
                                                                style="margin-right: 5px;" required>
                                                            <label for="region_{{ $loop->index }}"
                                                                style=" margin-top: 10px;">{{ $region }}</label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6 mt-3">
                                                <label class="red-label" for="previous_participation">Previous
                                                    Participation <span class="red-label">*</span></label>
                                                <div style="display: flex; flex-wrap: wrap; gap: 40px;"
                                                    class="form-check is-filled">
                                                    <div style="display: flex; align-items: center;">
                                                        <input class="form-check-input" type="radio"
                                                            name="previous_participation" value="1"
                                                            id="previous_participation_yes" {{ $isDisabled }}
                                                            {{ old('previous_participation', $application->participated_previous ?? '') == 1 ? 'checked' : '' }}
                                                            style="margin-right: 5px;" required>
                                                        <label for="previous_participation_yes"
                                                            style="margin-top: 10px;">Yes</label>
                                                    </div>
                                                    <div style="display: flex; align-items: center;">
                                                        <input class="form-check-input" type="radio"
                                                            name="previous_participation" value="0"
                                                            id="previous_participation_no" {{ $isDisabled }}
                                                            {{ old('previous_participation', $application->participated_previous ?? '') === 0 || old('previous_participation', $application->participated_previous ?? '') === '0' ? 'checked' : '' }}
                                                            style="margin-right: 5px;" required>
                                                        <label for="previous_participation_no"
                                                            style="margin-top: 10px;">No</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <hr class="custom-hr">

                                        <div class="row mt-3 align-items-center me-2">
                                            <!-- Stall Categories -->
                                            @php
                                                $countryList = config('constants.exhibition_cost');
                                            @endphp

                                            <div class="col-12 col-sm-6 d-flex align-items-start">
                                                <label for="stall_category"
                                                    class="red-label form-label me-3 mt-1 textB">Stall
                                                    Categories <span class="red-label">*</span></label>
                                                <div class="d-flex flex-wrap gap-3">
                                                    @foreach ($stall_type as $type)
                                                        <div class="form-check ">
                                                            <input class="form-check-input" type="radio"
                                                                name="stall_category" value="{{ $type }}"
                                                                id="stall_{{ $loop->index }}" {{ $isDisabled }}
                                                                {{ isset($application) && old('stall_category', $application->stall_category) == $type ? 'checked' : '' }}
                                                                required onchange="updateStallSize()" style="color:#000">
                                                            <label for="stall_{{ $loop->index }}"
                                                                class="form-check-label ms-2">{{ $type }}</label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <div class="col-12 col-sm-6 d-flex align-items-center">
                                                <label for="interested_sqm" class="red-label form-label me-3">
                                                    Interested SQM <span class="red-label">*</span>
                                                    <div id="stallSizeError" class="text-danger mt-2"
                                                        style="display: none;">
                                                        Please select a stall size.</div>
                                                </label>

                                                <div class="dropdown w-auto">
                                                    <button class="btn btn-secondary dropdown-toggle" type="button"
                                                        id="stallSizeDropdown" data-bs-toggle="dropdown"
                                                        aria-expanded="false"
                                                        style="max-height: 200px; overflow-y: auto; color: #FFFFFF">
                                                        Select Stall Size
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="stallSizeDropdown"
                                                        id="stall_size" style="max-height: 200px; overflow-y: auto;">
                                                        <li><a class="dropdown-item" href="#" data-value="">Select
                                                                Stall Size</a></li>
                                                    </ul>
                                                    <input type="hidden" name="interested_sqm" id="interested_sqm"
                                                        required>
                                                </div>
                                            </div>

                                            <script>
                                                // Stall size options for each category
                                                const countryList = @json($countryList);

                                                function updateStallSize() {
                                                    let selectedCategory = document.querySelector('input[name="stall_category"]:checked');
                                                    let stallSizeMenu = document.getElementById('stall_size');
                                                    let interestedSqmInput = document.getElementById('interested_sqm');
                                                    let stallSizeDropdown = document.getElementById('stallSizeDropdown');
                                                    stallSizeMenu.innerHTML = '';
                                                    interestedSqmInput.value = '';
                                                    stallSizeDropdown.textContent = 'Select Stall Size';

                                                    if (!selectedCategory) return;

                                                    let type = selectedCategory.value.toLowerCase();
                                                    let options = [];

                                                    // If type contains 'shell', use keys ending with 's', else use keys without 's'
                                                    if (type.includes('shell')) {
                                                        options = Object.entries(countryList).filter(([key, val]) => key.endsWith('s'));
                                                    } else {
                                                        options = Object.entries(countryList).filter(([key, val]) => !key.endsWith('s'));
                                                    }

                                                    // If no options for raw, fallback to all (for demo)
                                                    if (options.length === 0) {
                                                        options = Object.entries(countryList);
                                                    }

                                                    // Add default option
                                                    let defaultLi = document.createElement('li');
                                                    let defaultA = document.createElement('a');
                                                    defaultA.className = 'dropdown-item';
                                                    defaultA.href = '#';
                                                    defaultA.dataset.value = '';
                                                    defaultA.textContent = 'Select Stall Size';
                                                    defaultLi.appendChild(defaultA);
                                                    stallSizeMenu.appendChild(defaultLi);

                                                    // Add options
                                                    options.forEach(([key, label]) => {
                                                        let li = document.createElement('li');
                                                        let a = document.createElement('a');
                                                        a.className = 'dropdown-item';
                                                        a.href = '#';
                                                        a.dataset.value = key;
                                                        a.textContent = label;
                                                        a.onclick = function(e) {
                                                            e.preventDefault();
                                                            interestedSqmInput.value = key;
                                                            stallSizeDropdown.textContent = label;
                                                            document.getElementById('stallSizeError').style.display = 'none';
                                                        };
                                                        li.appendChild(a);
                                                        stallSizeMenu.appendChild(li);
                                                    });
                                                }

                                                // Set initial options if already selected
                                                document.addEventListener('DOMContentLoaded', function() {
                                                    updateStallSize();

                                                    // Restore selected value if exists
                                                    let interestedSqm = "{{ old('interested_sqm', $application->interested_sqm ?? '') }}";
                                                    if (interestedSqm) {
                                                        let selectedLabel = countryList[interestedSqm];
                                                        if (selectedLabel) {
                                                            document.getElementById('interested_sqm').value = interestedSqm;
                                                            document.getElementById('stallSizeDropdown').textContent = selectedLabel;
                                                        }
                                                    }
                                                });

                                                // Validate on submit
                                                document.getElementById('step1').addEventListener('submit', function(e) {
                                                    let interestedSqm = document.getElementById('interested_sqm').value;
                                                    if (!interestedSqm) {
                                                        document.getElementById('stallSizeError').style.display = '';
                                                        e.preventDefault();
                                                    }
                                                });
                                            </script>

                                            <div class="col-12 col-sm-6 d-flex align-items-center mb-3 mt-3">
                                                <label for="interested_sqm" class="red-label form-label me-3">
                                                    Applying for<span class="red-label">*</span>
                                                </label>
                                                <div class="form-check form-check-inline">
                                                    <input
                                                        class="form-check-input @error('sponsorship_apply') is-invalid @enderror"
                                                        type="radio" name="sponsorship_apply" id="sponsorship_no"
                                                        value="0"
                                                        {{ old('sponsorship_apply', $application->sponsor_only ?? null) == 0 || is_null(old('sponsorship_apply', $application->sponsor_only ?? null)) ? 'checked' : '' }}
                                                        {{ $isDisabled }} required>
                                                    <label class="form-check-label"
                                                        for="sponsorship_no">Exhibition</label>
                                                </div>

                                                <div class="form-check form-check-inline">
                                                    <input
                                                        class="form-check-input @error('sponsorship_apply') is-invalid @enderror"
                                                        type="radio" name="sponsorship_apply" id="sponsorship_yes"
                                                        value="1"
                                                        {{ old('sponsorship_apply', $application->sponsor_only ?? null) == 1 ? 'checked' : '' }}
                                                        {{ $isDisabled }} required>
                                                    <label class="form-check-label"
                                                        for="sponsorship_yes">Sponsorship</label>
                                                </div>

                                                @error('sponsorship_apply')
                                                    <div class="invalid-feedback d-block">
                                                        {{ $message }}
                                                    </div>
                                                @enderror
                                            </div>



                                        </div>
                                        <div class="row mt-3 ms-0">
                                            <div class="col-12 col-sm-6">
                                                <label for="exampleFormControlInput1" class="red-label form-label">Company
                                                    Name
                                                    <span class="red-label">*</span></label>
                                                <div class="input-group input-group-dynamic">

                                                    <input class="multisteps-form__input form-control ms-0" type="text"
                                                        name="company_name" id="companyName"
                                                        value="{{ $application->company_name ?? '' }}" required
                                                        {{ $isDisabled }} />
                                                </div>

                                            </div>
                                            <div class="col-12 col-sm-6 mt-3 mt-sm-0">
                                                <label for="exampleFormControlInput1"
                                                    class="form-label red-label ">Address
                                                    <span class="red-label">*</span></label>
                                                <div class="input-group input-group-dynamic">

                                                    <input class="multisteps-form__input form-control" type="text"
                                                        name="address" id="companyAddress" required {{ $isDisabled }}
                                                        length="120" value="{{ $application->address ?? '' }}" />
                                                </div>

                                            </div>
                                        </div>
                                        <div class="row mt-5 ms-0">

                                            <div class="col-12 col-sm-3 mt-3 mt-sm-0">
                                                <label for="city" class="form-label red-label mb-3">City <span
                                                        class="red-label">*</span></label>
                                                <div class="input-group input-group-dynamic">
                                                    <input class="multisteps-form__input form-control" type="text"
                                                        name="city" id="city"
                                                        value="{{ $application->city_id ?? '' }}" required
                                                        {{ $isDisabled }} />
                                                </div>

                                            </div>
                                            <div class="col-12 col-sm-3">
                                                <label for="postalCode" class="form-label red-label mb-3">
                                                    Postal Code <span class="red-label">*</span>
                                                </label>
                                                <div class="input-group input-group-dynamic">
                                                    <input class="multisteps-form__input form-control" type="text"
                                                        name="postal_code" value="{{ $application->postal_code ?? '' }}"
                                                        required {{ $isDisabled }} id="postalCode" pattern="\d*"
                                                        maxlength="10"
                                                        onkeypress="return event.charCode >= 48 && event.charCode <= 57" />
                                                </div>
                                            </div>

                                            <div class="col-12 col-sm-3 ms-0">
                                                <label class="ms-0 red-label">Country <span
                                                        class="red-label">*</span></label>
                                                @if ($isDisabled == 'disabled')
                                                    <input class="form-select form-select-lg mb-3 text-dark dropdown"
                                                        type="text" name="country" id="country"
                                                        value="{{ $application->country->name ?? '' }}" required
                                                        {{ $isDisabled }} />
                                                @else
                                                    <select class="form-select form-select-lg mb-3 text-dark dropdown"
                                                        name="country" id="country"
                                                        {{ $isDisabled !== 'disabled' ? 'required' : '' }}
                                                        {{ $isDisabled }}>
                                                        <option value="" disabled selected>Select Country</option>
                                                        @foreach ($countries as $country)
                                                            <option class="text-sm text-dark" value="{{ $country->id }}"
                                                                {{ isset($application) && $application->country_id == $country->id ? 'selected' : '' }}>
                                                                {{ $country->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                            </div>
                                            <div class="col-12 col-sm-3 mt-3 mt-sm-0">
                                                <label for="city" class="form-label red-label ">State <span
                                                        class="red-label">*</span></label>
                                                <div class="input-group input-group-dynamic">
                                                    @if ($isDisabled == 'disabled')
                                                        <input class="form-select form-select-lg mb-3 text-dark dropdown"
                                                            type="text" name="state" id="state"
                                                            value="{{ $application->state->name ?? '' }}" required
                                                            {{ $isDisabled }} />
                                                    @else
                                                        <select class="form-select form-select-lg mb-3 text-dark dropdown"
                                                            name="state" id="state" required {{ $isDisabled }}
                                                            onchange="validateState(this)">

                                                            <option value="" disabled selected>Select State</option>

                                                        </select>
                                                    @endif
                                                    <script>
                                                        function validateState(select) {
                                                            if (select.value === "") {
                                                                alert("Please select a billing state.");
                                                            }
                                                        }
                                                    </script>
                                                    <script>
                                                        $(document).ready(function() {
                                                            function loadStates(countryId, selectedStateId = null) {
                                                                if (countryId) {
                                                                    $.ajax({
                                                                        url: "{{ route('get.states') }}",
                                                                        type: "POST",
                                                                        data: {
                                                                            country_id: countryId,
                                                                            _token: "{{ csrf_token() }}"
                                                                        },
                                                                        success: function(states) {
                                                                            var stateDropdown = $('select[name="state"]');
                                                                            stateDropdown.empty().append(
                                                                                '<option value="" disabled selected>Select State</option>');

                                                                            $.each(states, function(key, state) {
                                                                                // if (selectedStateId == state.id) {
                                                                                //     console.log('selected state', state.name)
                                                                                // }
                                                                                stateDropdown.append('' +
                                                                                    '<option value="' + state.id + '" ' +
                                                                                    (selectedStateId == state.id ? 'selected' : '') + '>' +
                                                                                    state.name + '</option>');
                                                                            });
                                                                        },
                                                                        error: function() {
                                                                            alert("Error fetching states. Please try again.");
                                                                        }
                                                                    });
                                                                } else {
                                                                    // console.log('emptying states');
                                                                    $('select[name="state"]').empty().append(
                                                                        '<option value="" disabled selected>Select State</option>');
                                                                }
                                                            }

                                                            // Load states on page load if billing_country_id exists
                                                            var selectedCountryId = "{{ $application->country_id ?? '' }}";
                                                            var selectedStateId = "{{ $application->state_id ?? '' }}";
                                                            var load = false;

                                                            if (selectedCountryId && selectedStateId) {
                                                                // console.log(selectedCountryId, selectedStateId);
                                                                loadStates(selectedCountryId, selectedStateId);
                                                            }

                                                            // Update states when country dropdown changes
                                                            $('select[name="country"]').change(function() {
                                                                var countryId = $(this).val();
                                                                loadStates(countryId);
                                                            });
                                                        });
                                                    </script>

                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-5 ms-0">
                                            <div class="col-12 col-sm-3">
                                                <label for="contactNo" class="form-label red-label ">Company Landline No
                                                    <span class="red-label">*</span></label>
                                                <div class="input-group input-group-dynamic">
                                                    <div class="d-flex">
                                                        <select class="form-control" id="choices-languages"
                                                            name="contactNoCode" style="max-width: 80px;"
                                                            {{ $isDisabled }}>
                                                            @foreach ($countries->unique('code') as $country)
                                                                <option value="{{ $country->code }}"
                                                                    {{ (isset($application) && explode('-', $application->landline)[0] == $country->code) || (!isset($application) && $country->code == '91') ? 'selected' : '' }}>
                                                                    {{ $country->code }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        <input class="multisteps-form__input form-control" type="tel"
                                                            name="company_no" id="contactNo"
                                                            value="{{ isset($application) ? explode('-', $application->landline)[1] ?? '' : '' }}"
                                                            required {{ $isDisabled }} pattern="\d*" minlength="6"
                                                            maxlength="14"
                                                            onkeypress="return event.charCode >= 48 && event.charCode <= 57" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-3 mt-3 mt-sm-0">
                                                <label for="email" class="form-label red-label ">Company E-Mail <span
                                                        class="red-label">*</span></label>
                                                <div class="input-group input-group-dynamic">

                                                    <input class="multisteps-form__input form-control" type="email"
                                                        name="company_email" id="company_email"
                                                        value="{{ $application->company_email ?? '' }}" required
                                                        {{ $isDisabled }} />
                                                </div>

                                            </div>
                                            <div class="col-12 col-sm-3 mt-3 mt-sm-0">
                                                <label for="website" class="form-label red-label ">Website </label>
                                                <div class="input-group input-group-dynamic">

                                                    <input class="multisteps-form__input form-control" type="text"
                                                        name="website" pattern="(https:\/\/|www\.).*"
                                                        value="{{ $application->website ?? '' }}" {{ $isDisabled }} />

                                                </div>
                                                <div class="text-end">
                                                    <p class="text-xxs">{{ config('constants.EVENT_WEBSITE') }}</p>

                                                </div>
                                            </div>
                                        </div>
                                        {{-- <div class="col-6 col-sm-3 ms-2 mt-4">
                                            <label class="ms-0 red-label ">Your Company Headquarter Country <span
                                                    class="red-label">*</span></label>
                                            <select class="form-select form-select-lg mb-3 text-dark dropdown"
                                                name="headquarters_country" id="headquarters_country"
                                                {{ $isDisabled !== 'disabled' ? 'required' : '' }} {{ $isDisabled }}>
                                                <option value="" disabled selected>Select Country</option>
                                                @foreach ($countries as $country)
                                                    <option class="text-sm text-dark" value="{{ $country->id }}"
                                                        {{ isset($application) && $application->headquarters_country_id == $country->id ? 'selected' : '' }}>
                                                        {{ $country->name }}

                                                    </option>
                                                @endforeach
                                            </select>
                                        </div> --}}
                                        <div class="text-xxs font-weight-bold text-dark ms-2 " style="font-size: 7rem;">
                                            <p class="font-weight-bold text-lg black-label" style="font-size: 7rem;">
                                                Event Contact
                                                Person Details: </p>
                                        </div>
                                        <div class="row mt-4 ms-0">
                                            <div class="col-md-4 col-sm-4 mt-0 mt-sm-0">

                                                <label class="form-control red-label ">Title <span
                                                        class="red-label">*</span></label>
                                                <div class="input-group input-group-dynamic">
                                                    <select class="form-control" name="event_contact_salutation"
                                                        id="salutation" required {{ $isDisabled }}>
                                                        <option value="Mr."
                                                            {{ isset($eventContact) && $eventContact->salutation == 'Mr.' ? 'selected' : '' }}>
                                                            Mr.
                                                        </option>
                                                        <option value="Ms."
                                                            {{ isset($eventContact) && $eventContact->salutation == 'Ms.' ? 'selected' : '' }}>
                                                            Ms.
                                                        </option>
                                                        <option value="Mrs."
                                                            {{ isset($eventContact) && $eventContact->salutation == 'Mrs.' ? 'selected' : '' }}>
                                                            Mrs.
                                                        </option>
                                                        <option value="Dr."
                                                            {{ isset($eventContact) && $eventContact->salutation == 'Dr.' ? 'selected' : '' }}>
                                                            Dr.
                                                        </option>
                                                        <option value="Prof."
                                                            {{ isset($eventContact) && $eventContact->salutation == 'Prof.' ? 'selected' : '' }}>
                                                            Prof.
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-4  mt-sm-0">
                                                <label for="firstName" class="form-label red-label ">First Name <span
                                                        class="red-label">*</span></label>
                                                <div class="input-group input-group-dynamic">

                                                    <input class="multisteps-form__input form-control mt-2" type="text"
                                                        name="event_contact_first_name" id="event_contact_first_name"
                                                        value="{{ $eventContact->first_name ?? '' }}" required
                                                        {{ $isDisabled }} />
                                                </div>

                                            </div>
                                            <div class="col-md-4 col-sm-4 mt-4 mt-sm-0">
                                                <label for="lastName" class="form-label red-label ">Last Name <span
                                                        class="red-label">*</span></label>
                                                <div class="input-group input-group-dynamic">

                                                    <input class="multisteps-form__input form-control mt-2" type="text"
                                                        name="event_contact_last_name" id="event_contact_last_name"
                                                        value="{{ $eventContact->last_name ?? '' }}" required
                                                        {{ $isDisabled }} />
                                                </div>

                                            </div>
                                        </div>
                                        <div class="row mt-5 ms-0">
                                            <div class="col-12 col-sm-4">
                                                <label for="designation" class="form-label red-label ">Job Title <span
                                                        class="red-label">*</span></label>
                                                <div class="input-group input-group-dynamic">

                                                    <input class="multisteps-form__input form-control" type="text"
                                                        name="event_contact_designation" id="designation"
                                                        value="{{ $eventContact->job_title ?? '' }}" {{ $isDisabled }}
                                                        required />
                                                </div>

                                            </div>
                                            <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                                <label for="contactEmail" class="form-label red-label ">Contact E-Mail
                                                    <span class="red-label">*</span></label>
                                                <div class="input-group input-group-dynamic">

                                                    <input class="multisteps-form__input form-control" type="email"
                                                        name="event_contact_email" id="contactEmail"
                                                        value="{{ $eventContact->email ?? '' }}" required
                                                        {{ $isDisabled }} />
                                                </div>

                                            </div>
                                            <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                                <label for="contactPhone" class="form-label red-label ">Contact Number
                                                    <span class="red-label">*</span></label>
                                                <div class="input-group input-group-dynamic">
                                                    <select class="form-control" id="choices-languages"
                                                        name="contactPhone_code" style="max-width: 80px;"
                                                        {{ $isDisabled }}>
                                                        @foreach ($countries->unique('code') as $country)
                                                            <option value="{{ $country->code }}"
                                                                {{ (isset($eventContact) && explode('-', $eventContact->contact_number)[0] == $country->code) || (!isset($application) && $country->code == '91') ? 'selected' : '' }}>
                                                                {{ $country->code }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <input class="multisteps-form__input form-control" type="tel"
                                                        name="event_contact_phone" id="contactPhone"
                                                        value="{{ isset($eventContact) ? explode('-', $eventContact->contact_number)[1] ?? '' : '' }}"
                                                        required {{ $isDisabled }} pattern="\d*" minlength="6"
                                                        maxlength="14"
                                                        onkeypress="return event.charCode >= 48 && event.charCode <= 57" />
                                                </div>

                                            </div>
                                        </div>


                                        
                                        {{-- <div class="row">
                                            <div class="col-12 ms-0">
                                                <label class="mt-4 form-label red-label ">Type of Business: <small
                                                        style="font-weight: normal; font-size: 2px;">(Multiple entries
                                                        possible)</small> <span class="red-label">*</span></label>
                                                <div class="row ms-2">
                                                    @foreach ($business as $id => $name)
                                                        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox"
                                                                    id="type_of_business_{{ $id }}"
                                                                    name="type_of_business[]" value="{{ $name }}"
                                                                    {{ $isDisabled }}
                                                                    {{ isset($application) && in_array($name, explode(',', $application->type_of_business)) ? 'checked' : '' }}>
                                                                <label class="form-check-label"
                                                                    for="type_of_business_{{ $id }}">{{ $name }}</label>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>

                                            <input type="hidden" name="type_of_business_validation"
                                                id="type_of_business_validation" required>
                                        </div> --}}
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <div class="row">
                                                    <div class="col-6">
                                                        <div class="">
                                                            <p class="ms-1 text-lg black-label red-label">Billing
                                                                Details:</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">

                                                        <div class="form-check ms-auto">
                                                            <input class="form-check-input" type="checkbox"
                                                                id="copyCompanyDetails" onclick="copyDetails()"
                                                                {{ $isDisabled }}>
                                                            <label class="form-check-label" for="copyCompanyDetails">Same
                                                                as
                                                                Company Details</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-5">
                                            <div class="col-12 col-sm-4">
                                                <label for="billingCompany" class="form-label red-label ">Billing
                                                    Company
                                                    <span class="red-label">*</span></label>
                                                <div class="input-group input-group-dynamic">

                                                    <input class="multisteps-form__input form-control" type="text"
                                                        name="billing_company" id="billing_company"
                                                        value="{{ isset($application) ? $billing->billing_company ?? '' : '' }}"
                                                        required {{ $isDisabled }} />
                                                </div>

                                            </div>
                                            <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                                <label for="contactName" class="form-label red-label ">Contact Name
                                                    <span class="red-label">*</span></label>
                                                <div class="input-group input-group-dynamic">
                                                    <input class="multisteps-form__input form-control" type="text"
                                                        name="billing_contact_name" id="billing_contact_name"
                                                        value="{{ isset($application) ? $billing->contact_name ?? '' : '' }}"
                                                        required {{ $isDisabled }} />
                                                </div>

                                            </div>
                                            <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                                                <label for="contactEmail" class="form-label red-label ">Billing E-Mail
                                                    <span class="red-label">*</span></label>
                                                <div class="input-group input-group-dynamic">

                                                    <input class="multisteps-form__input form-control" type="email"
                                                        name="billing_email" id="billing_email"
                                                        value="{{ isset($billing) ? $billing->email ?? '' : '' }}"
                                                        required {{ $isDisabled }} />
                                                </div>

                                            </div>
                                        </div>
                                        <div class="row mt-5">
                                            <div class="col-12 col-sm-4">
                                                <label for="billingAddress" class="form-label red-label ">Billing Phone
                                                    Number
                                                    <span class="red-label">*</span></label>
                                                <div class="input-group input-group-dynamic">
                                                    <select class="form-control" id="choices-languages"
                                                        name="billing_phoneCode" style="max-width: 60px;"
                                                        {{ $isDisabled }}>

                                                        @foreach ($countries->unique('code') as $countryName)
                                                            <option value="{{ $countryName->code }}"
                                                                {{ (isset($eventContact) && explode('-', $billing->phone)[0] == $countryName->code) || (!isset($application) && $countryName->code == '91') ? 'selected' : '' }}>
                                                                {{ $countryName->code }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <input class="multisteps-form__input form-control" type="tel"
                                                        name="billing_phone" id="billing_phone"
                                                        value="{{ isset($billing->phone) ? explode('-', $billing->phone)[1] ?? '' : '' }}"
                                                        required {{ $isDisabled }} pattern="\d*" minlength="6"
                                                        maxlength="14"
                                                        onkeypress="return event.charCode >= 48 && event.charCode <= 57" />
                                                </div>

                                            </div>
                                            <div class="col-12 col-sm-8 mt-3 mt-sm-0">
                                                <label for="billingAddress" class="form-label red-label ">Billing
                                                    Address
                                                    <span class="red-label">*</span></label>
                                                <div class="input-group input-group-dynamic">

                                                    <input class="multisteps-form__input form-control" type="text"
                                                        name="billing_address" id="billing_address" required
                                                        {{ $isDisabled }}
                                                        value="{{ isset($application) ? $billing->address ?? '' : '' }}" />
                                                </div>

                                            </div>
                                        </div>
                                        <div class="row mt-5">
                                            <div class="col-12 col-sm-3 mt-3 mt-sm-0">
                                                <label for="billingCity" class="form-label red-label mb-3 ">Billing City
                                                    <span class="red-label">*</span></label>
                                                <div class="input-group input-group-dynamic">
                                                    <input class="multisteps-form__input form-control" type="text"
                                                        id="billing_city" name="billing_city"
                                                        value="{{ isset($application) ? $billing->city_id ?? '' : '' }}"
                                                        required {{ $isDisabled }} />
                                                </div>

                                            </div>
                                            <div class="col-12 col-sm-3">
                                                <label for="billingPostalCode" class="form-label red-label mb-3 ">Billing
                                                    Postal
                                                    Code
                                                    <span class="red-label">*</span></label>
                                                <div class="input-group input-group-dynamic">

                                                    <input class="multisteps-form__input form-control" type="text"
                                                        name="billing_postal_code" id="billing_postal_code"
                                                        value="{{ isset($application) ? $billing->postal_code ?? '' : '' }}"
                                                        required {{ $isDisabled }} pattern="\d*" maxlength="10"
                                                        onkeypress="return event.charCode >= 48 && event.charCode <= 57" />
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-3 ms-0">
                                                <label class="ms-0 red-label">Billing Country <span
                                                        class="red-label">*</span></label>
                                                <select class=" form-select form-select-lg mb-3 text-dark dropdown "
                                                    name="billing_country" id="billing_country"
                                                    {{ $isDisabled !== 'disabled' ? 'required' : '' }}
                                                    {{ $isDisabled }}>
                                                    <option value="" disabled selected>Select Country</option>
                                                    @foreach ($countries as $country)
                                                        <option class="text-sm text-dark" value="{{ $country->id }}"
                                                            {{ isset($application) && $application->billing_country_id == $country->id ? 'selected' : '' }}>
                                                            {{ $country->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <script></script>
                                            {{--                                            @dd($application->billingDetail) --}}
                                            {{--                                            {{$application->billingDetail->state->name ?? 'test'}} --}}
                                            <div class="col-12 col-sm-3 mt-3 mt-sm-0">
                                                <label for="billingState" class="form-label red-label ">Billing State
                                                    <span class="red-label">*</span></label>
                                                <div class="input-group input-group-dynamic">
                                                    @if ($isDisabled === 'disabled')
                                                        <input class="form-select form-select-lg mb-3 form-control"
                                                            type="text" name="billing_state" id="billingState"
                                                            value="{{ isset($billing->state) ? $billing->state->name : '' }}"
                                                            required {{ $isDisabled }} />
                                                    @else
                                                        <select class="form-control" id="billing_state"
                                                            name="billing_state" required {{ $isDisabled }}
                                                            onchange="validateBillingState(this)">

                                                            <option value="" disabled selected>Select Billing State
                                                            </option>

                                                        </select>
                                                    @endif

                                                    <script>
                                                        document.addEventListener("DOMContentLoaded", function() {
                                                            let Country = document.getElementById("country"); // Billing country dropdown
                                                            let contactCode = document.querySelector(
                                                            "select[name='contactNoCode']"); // Landline country code dropdown
                                                            let contactPhoneCode = document.querySelector(
                                                            "select[name='contactPhone_code']"); // Phone country code dropdown
                                                            let billingPhoneCode = document.querySelector("select[name='billing_phoneCode']");
                                                            let sec_contactPhoneCode = document.querySelector("select[name='sec_contactPhone_code']");
                                                            // Phone country code dropdown
                                                            //console.log(billingCountry);
                                                            //console.log(contactCode.value, contactPhoneCode.value, billingPhoneCode.value);

                                                            Country.addEventListener("change", function() {
                                                                let selectedCountryId = Country.value; // Get selected country code

                                                                if (selectedCountryId) {
                                                                    $.ajax({
                                                                        url: "/get-country-code",
                                                                        type: "POST",
                                                                        data: {
                                                                            country_id: selectedCountryId,
                                                                            _token: "{{ csrf_token() }}"
                                                                        },
                                                                        success: function(response) {
                                                                            let countryCode = response.code;
                                                                            //console.log('Country code:', countryCode);
                                                                            contactCode.value = countryCode; // Update landline country code
                                                                            contactPhoneCode.value = countryCode; // Update phone country code
                                                                            billingPhoneCode.value =
                                                                            countryCode; // Update billing phone country code
                                                                            sec_contactPhoneCode.value = countryCode;
                                                                        },
                                                                        error: function() {
                                                                            alert("Error fetching country code. Please try again.");
                                                                        }
                                                                    });
                                                                }
                                                            });

                                                            function validateBillingState(select) {
                                                                if (select.value === "") {
                                                                    alert("Please select a billing state.");
                                                                }
                                                            }
                                                        });
                                                    </script>
                                                    <script>
                                                        $(document).ready(function() {
                                                            function loadStates(countryId, selectedStateId = null) {
                                                                if (countryId) {
                                                                    const divform = document.getElementById('card-body');
                                                                    const form_step = document.getElementById('step1');
                                                                    const gst_india = document.getElementById('gst_india');
                                                                    const gst_compliance = document.getElementById('choices-sizes');
                                                                    let gstNo = document.getElementById('gst_no');
                                                                    let pan_no = document.getElementById('pan_no');


                                                                    $.ajax({
                                                                        url: "{{ route('get.states') }}",
                                                                        type: "POST",
                                                                        data: {
                                                                            country_id: countryId,
                                                                            _token: "{{ csrf_token() }}"
                                                                        },
                                                                        success: function(states) {
                                                                            var stateDropdown = $('select[name="billing_state"]');
                                                                            stateDropdown.empty().append(
                                                                                '<option value="" disabled selected>Select State</option>');

                                                                            $.each(states, function(key, state) {
                                                                                //check if the selectedStateId and state.id
                                                                                stateDropdown.append('<option value="' + state.id + '" ' +
                                                                                    (selectedStateId == state.id ? 'selected' : '') + '>' +
                                                                                    state.name + '</option>');
                                                                            });
                                                                        },
                                                                        error: function() {
                                                                            alert("Error fetching states. Please try again.");
                                                                        }
                                                                    });
                                                                } else {
                                                                    // console.log('empty country 2');
                                                                    $('select[name="billing_state"]').empty().append(
                                                                        '<option value="" disabled selected>Select State</option>');
                                                                }
                                                            }

                                                            // Load states on page load if billing_country_id exists
                                                            var selectedCountryId = "{{ $application->billingDetails->country_id ?? '' }}";
                                                            var selectedStateId =
                                                            "{{ $application->billingDetail->state_id ?? '' }}"; // Assuming a stored state ID exists
                                                            if (selectedCountryId) {
                                                                console.log('Selected country ID:', selectedCountryId);
                                                                loadStates(selectedCountryId, selectedStateId);
                                                            }

                                                            // Update states when country dropdown changes
                                                            $('select[name="billing_country"]').change(function() {
                                                                var countryId = $(this).val();
                                                                // console.log('countryId', countryId);
                                                                loadStates(countryId);
                                                            });
                                                        });
                                                    </script>
                                                    <script>
                                                        $(document).ready(function() {
                                                            function loadStates(countryId, selectedStateId = null) {
                                                                if (countryId) {
                                                                    const divform = document.getElementById('card-body');
                                                                    const form_step = document.getElementById('step1');

                                                                    $.ajax({
                                                                        url: "{{ route('get.states') }}",
                                                                        type: "POST",
                                                                        data: {
                                                                            country_id: countryId,
                                                                            _token: "{{ csrf_token() }}"
                                                                        },
                                                                        success: function(states) {
                                                                            var stateDropdown = $('select[name="billing_state"]');
                                                                            stateDropdown.empty().append(
                                                                                '<option value="" disabled selected>Select State</option>');

                                                                            $.each(states, function(key, state) {
                                                                                stateDropdown.append('<option value="' + state.id + '" ' +
                                                                                    (selectedStateId == state.id ? 'selected' : '') + '>' +
                                                                                    state.name + '</option>');
                                                                            });
                                                                        },
                                                                        error: function() {
                                                                            alert("Error fetching states. Please try again.");
                                                                        }
                                                                    });
                                                                } else {
                                                                    //console.log('empty country 3');
                                                                    $('select[name="billing_state"]').empty().append(
                                                                        '<option value="" disabled selected>Select State</option>');
                                                                }
                                                            }

                                                            // Load states on page load if billing_country_id exists
                                                            var selectedCountryId = "{{ $application->billing_country_id ?? '' }}";
                                                            var selectedStateId =
                                                            "{{ $application->billingDetail->state_id ?? '' }}"; // Assuming a stored state ID exists

                                                            if (selectedCountryId) {
                                                                // console.log('Selected country ID:', selectedCountryId);
                                                                loadStates(selectedCountryId, selectedStateId);
                                                            }

                                                            // Update states when country dropdown changes
                                                            $('select[name="billing_country"]').change(function() {
                                                                var countryId = $(this).val();
                                                                loadStates(countryId);
                                                            });
                                                        });
                                                    </script>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2 ">
                                            <div class="col-md-3 col-sm-6 col-12 mt-3">
                                                <label class="multisteps-form__input ms-0 red-label form-label">GST
                                                    Compliance <span class="red-label ">*</span></label>
                                                <select class="form-select form-select-lg mb-3 text-dark dropdown"
                                                    name="gst_compliance" id="choices-sizes" {{ $isDisabled }}
                                                    onchange="toggleGstNoRequired(this)">
                                                    {{-- <option value disabled selected>Select GST Compliance</option> --}}
                                                    <option value="1"
                                                        {{ isset($application) && $application->gst_compliance == 1 ? 'selected' : '' }}>
                                                        Yes
                                                    </option>
                                                    <option value="0"
                                                        {{ isset($application) && $application->gst_compliance == 0 ? 'selected' : '' }}>
                                                        No
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="col-md-3 col-sm-6 col-12 mt-3">
                                                <label for="gstNo" id="gstlabel" class="form-label red-label">
                                                    GST Number <span id="gstStar" class="red-label"
                                                        style="display:none;">*</span>
                                                </label>
                                                <div class="input-group input-group-dynamic">
                                                    <input class="multisteps-form__input form-control" type="text"
                                                        name="gst_no" id="gstNo"
                                                        value="{{ $application->gst_no ?? '' }}" {{ $isDisabled }}>
                                                </div>
                                                <div class="text-end">
                                                    <p class="text-xs">e.g. 22AAAAA0000A1Z5</p>
                                                </div>
                                            </div>

                                            <script>
                                                function toggleGstNoRequired(select) {
                                                    var gstNo = document.getElementById('gstNo');
                                                    var gstStar = document.getElementById('gstStar');
                                                    if (select.value == "1") {
                                                        gstNo.required = true;
                                                        gstStar.style.display = '';
                                                    } else {
                                                        gstNo.required = false;
                                                        gstStar.style.display = 'none';
                                                    }
                                                }
                                                document.addEventListener('DOMContentLoaded', function() {
                                                    var gstCompliance = document.getElementById('choices-sizes');
                                                    toggleGstNoRequired(gstCompliance);
                                                });
                                            </script>

                                            <div class="col-md-3 col-sm-6 col-12 mt-3">
                                                <label id="pan_label" for="pan_no" class="form-label red-label">PAN
                                                    Number <span class="red-label"> *</span></label>
                                                <div class="input-group input-group-dynamic">
                                                    <input class="multisteps-form__input form-control" type="text"
                                                        name="pan_no" id="pan_no"
                                                        value="{{ $application->pan_no ?? '' }}"
                                                        pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}" {{ $isDisabled }} required>
                                                </div>
                                                <div class="text-end">
                                                    <p class="text-xs">e.g. XYZPK8200S</p>

                                                </div>
                                            </div>

                                            <div class="col-md-3 col-sm-6 col-12 mt-3">
                                                <label for="tan_no" class="form-label red-label">TAN Number</label>
                                                <div class="input-group input-group-dynamic">
                                                    <input class="multisteps-form__input form-control" type="text"
                                                        name="tan_no" id="tan_no"
                                                        value="{{ $application->tan_no ?? null }}"
                                                        pattern="[A-Z]{4}[0-9]{5}[A-Z]{1}" {{ $isDisabled }}>
                                                </div>
                                                <div class="text-end">
                                                    <p class="text-xs">e.g. ABCD12345X</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3 justify-content-center">
                                            <div class="col-12 d-flex justify-content-center">
                                                @if (isset($application) &&
                                                        ($application->submission_status == 'submitted' ||
                                                            $application->submission_status == 'approved' ||
                                                            $application->submission_status == 'rejected'))
                                                    <a href="{{ route('application.show') }}"
                                                        class="btn btn-info mb-2 w-fixed h-fixed js-btn-next">Next</a>
                                                @else
                                                    <button class="btn btn-info mb-2 w-fixed h-fixed js-btn-next"
                                                        type="submit">
                                                        Submit
                                                    </button>
                                                @endif
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function copyDetails() {
            if (document.getElementById("copyCompanyDetails").checked) {
                // Capture company details
                var companyDetails = {
                    companyName: document.getElementById("companyName").value,
                    firstName: document.getElementById("event_contact_first_name").value,
                    lastName: document.getElementById("event_contact_last_name").value,
                    email: document.getElementById("contactEmail").value,
                    contactNo: document.getElementById("contactPhone").value,
                    companyAddress: document.getElementById("companyAddress").value,
                    city: document.getElementById("city").value,
                    postalCode: document.getElementById("postalCode").value,
                    country: document.getElementById("country").value,
                    state: document.getElementById("state").value
                };

                // console.log("Copying company details:", companyDetails);

                // Fill in billing details
                document.getElementById("billing_company").value = companyDetails.companyName;
                document.getElementById("billing_contact_name").value = companyDetails.firstName + ' ' + companyDetails
                    .lastName;
                document.getElementById("billing_email").value = companyDetails.email;
                document.getElementById("billing_phone").value = companyDetails.contactNo;
                document.getElementById("billing_address").value = companyDetails.companyAddress;
                document.getElementById("billing_city").value = companyDetails.city;
                document.getElementById("billing_postal_code").value = companyDetails.postalCode;
                document.getElementById("billing_country").value = companyDetails.country;
                // document.getElementById("billing_state").value = companyDetails.state;

                // Trigger state population after country is set
                loadBillingStates(companyDetails.country, companyDetails.state);
            } else {
                // Clear billing details if checkbox is unchecked
                document.getElementById("billing_company").value = "";
                document.getElementById("billing_contact_name").value = "";
                document.getElementById("billing_email").value = "";
                document.getElementById("billing_phone").value = "";
                document.getElementById("billing_address").value = "";
                document.getElementById("billing_city").value = "";
                document.getElementById("billing_postal_code").value = "";
                document.getElementById("billing_country").value = "";
                document.getElementById("billing_state").innerHTML =
                    '<option value="" disabled selected>Select State</option>';
            }
        }


        function loadBillingStates(countryId, selectedStateId = null) {
            if (countryId) {
                const divform = document.getElementById('card-body');
                const form_step = document.getElementById('step1');
                const gst_india = document.getElementById('gst_india');
                const gst_compliance = document.getElementById('choices-sizes');


                $.ajax({
                    url: "{{ route('get.states') }}",
                    type: "POST",
                    data: {
                        country_id: countryId,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(states) {
                        var stateDropdown = $('select[name="billing_state"]');
                        stateDropdown.empty().append(
                        '<option value="" disabled selected>Select State</option>');

                        $.each(states, function(key, state) {
                            stateDropdown.append('<option value="' + state.id + '" ' +
                                (selectedStateId == state.id ? 'selected' : '') + '>' + state.name +
                                '</option>');
                        });

                        // console.log("Billing states loaded:", states);

                        // Ensure the correct state is selected after options are populated
                        if (selectedStateId) {
                            stateDropdown.val(selectedStateId).change();
                            //console.log("Billing state set to:", selectedStateId);
                        }
                    },
                    error: function() {
                        alert("Error fetching states. Please try again.");
                    }
                });
            } else {
                // console.log('empty country 4');
                $('select[name="billing_state"]').empty().append(
                '<option value="" disabled selected>Select State</option>');
            }
        }
    </script>



    {{--    Auto select of different country --}}

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let Country = document.getElementById("country"); // Billing country dropdown
            let HeadCountry = document.getElementById("headquarters_country");
            let billingCountry = document.getElementById("billing_country");
            Country.addEventListener("change", function() {
                let selectedCountryId = Country.value; // Get selected country code

                if (selectedCountryId) {
                    $.ajax({
                        url: "/get-country-code",
                        type: "POST",
                        data: {
                            country_id: selectedCountryId,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            let country = response.id;
                            HeadCountry.value = country; // Update landline country code
                            billingCountry.value = country; // Update phone country code
                            //console.log('Country code:', country);
                            loadBillingStates(Country.value);
                        },
                        error: function() {
                            alert("Error fetching country code. Please try again.");
                        }
                    });
                }
            });
        });
    </script>
@endsection
