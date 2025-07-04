@extends('layouts.users')
@section('title', $slug)
@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.min.css" />

    <style>
        .custom-label {
            font-size: 1rem !important;
        }

        @media (max-width: 767.98px) {
            .custom-height {
                height: 1150px;
            }
        }

        @media (min-width: 768px) {
            .custom-height {
                height: 850px;
            }
        }

        .iti {
            width: 100%;
        }
    </style>

    @php
        $cssClass = !empty(optional($exhibitorInfo)->fascia_name) ? 'is-filled' : '';

        $contactPerson = optional($exhibitorInfo)->contact_person ?? '';
        $salutation = '';
        $firstName = '';
        $lastName = '';

        if ($contactPerson) {
            if (preg_match('/^([A-Za-z\.]+)\s+([^\s]+)\s*(.*)$/', $contactPerson, $matches)) {
                $salutation = trim($matches[1]);
                $firstName = trim($matches[2]);
                $lastName = trim($matches[3]);
            }
        }
    @endphp

    <div class="container mt-4">
        <div class="card">
            <div class="card-body">
                <form class="multisteps-form__form custom-height" method="POST" action="{{ route('exhibitor.info.submit') }}"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="multisteps-form__panel border-radius-xl bg-white js-active" data-animation="FadeIn">
                        <h5 class="font-weight-bolder mb-0">Exhibitor Information</h5>
                        <p class="mb-5 text-sm">Prefilled details & mandatory exhibitor inputs</p>

                        <div class="multisteps-form__content">
                            <div class="row mt-5">
                                <div class="col-sm-6">
                                    <div class="input-group input-group-dynamic is-filled">
                                        <label class="form-label custom-label">Company Name</label>
                                        <input class="form-control" type="text" value="{{ $application->company_name }}" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-6 mt-3 mt-sm-0">
                                    <div class="input-group input-group-dynamic is-filled">
                                        <label class="form-label">Booth Number</label>
                                        <input class="form-control" type="text" value="{{ $application->stallNumber }}" readonly>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-5">
                                <div class="col-sm-6">
                                    <div class="input-group input-group-dynamic {{ $cssClass }}">
                                        <label class="form-label">Fascia Name</label>
                                        <input class="form-control" type="text" name="fascia_name"
                                            value="{{ optional($exhibitorInfo)->fascia_name }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-5">
                                <div class="col-sm-6">
                                    <div class="row">
                                        <div class="col-4 pe-1">
                                            <div class="input-group input-group-dynamic is-filled">
                                                <label class="form-label">Salutation</label>
                                                <select class="form-control" name="salutation" required>
                                                    <option value="" disabled selected>Select</option>
                                                    @foreach (['Mr.', 'Ms.', 'Mrs.', 'Dr.', 'Prof.'] as $option)
                                                        <option value="{{ $option }}" {{ $salutation == $option ? 'selected' : '' }}>{{ $option }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-4 px-1">
                                            <div class="input-group input-group-dynamic {{ $cssClass }}">
                                                <label class="form-label">First Name</label>
                                                <input class="form-control" type="text" name="contact_first_name"
                                                    value="{{ $firstName }}" required>
                                            </div>
                                        </div>
                                        <div class="col-4 ps-1">
                                            <div class="input-group input-group-dynamic {{ $cssClass }}">
                                                <label class="form-label">Last Name</label>
                                                <input class="form-control" type="text" name="contact_last_name"
                                                    value="{{ $lastName }}" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6 mt-3 mt-sm-0">
                                    <div class="input-group input-group-dynamic {{ $cssClass }}">
                                        <label class="form-label">Email Address</label>
                                        <input class="form-control" type="email" name="email"
                                            value="{{ optional($exhibitorInfo)->email }}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-5">
                                <div class="col-sm-6">
                                    <div class="input-group input-group-dynamic is-filled {{ $cssClass }}">
                                        <label class="form-label">Phone Number</label>
                                        <input id="phone" class="form-control" type="tel" name="phone"
                                            value="{{ optional($exhibitorInfo)->phone }}" required>
                                    </div>
                                </div>

                                <div class="col-sm-6 mt-3 mt-sm-0">
                                    <div class="input-group input-group-dynamic is-filled {{ $cssClass }}">
                                        <label class="form-label">Upload Logo</label>
                                        <input class="form-control" type="file" name="logo" accept="image/*"
                                            @if (empty(optional($exhibitorInfo)->logo)) required @endif>
                                        @if (!empty(optional($exhibitorInfo)->logo))
                                            <div class="mt-2">
                                                <img src="{{ asset('storage/' . optional($exhibitorInfo)->logo) }}"
                                                    alt="Uploaded Logo" style="max-height: 60px;">
                                                <small class="text-success d-block">Logo already uploaded.</small>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-5">
                                <div class="col-12">
                                    <label class="form-label">Company Description</label>
                                    <div class="input-group input-group-dynamic is-filled">
                                        <textarea class="form-control" name="description" id="description" rows="3" maxlength="750" required
                                            oninput="updateCharCount()">{{ optional($exhibitorInfo)->description }}</textarea>
                                    </div>
                                    <small id="charCount" class="text-muted">0 / 750 characters</small>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="row mt-5">
                                <div class="col-sm-6">
                                    <div class="input-group input-group-dynamic {{ $cssClass }} ">
                                        <label class="form-label">LinkedIn</label>
                                        <input class="form-control" type="url" name="linkedin"
                                            value="{{ optional($exhibitorInfo)->linkedin }}">
                                    </div>
                                </div>
                                <div class="col-sm-6 mt-3 mt-sm-0">
                                    <div class="input-group input-group-dynamic {{ $cssClass }}">
                                        <label class="form-label">Instagram</label>
                                        <input class="form-control" type="url" name="instagram"
                                            value="{{ optional($exhibitorInfo)->instagram }}">
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-5">
                                <div class="col-sm-6">
                                    <div class="input-group input-group-dynamic {{ $cssClass }}">
                                        <label class="form-label">Facebook</label>
                                        <input class="form-control" type="url" name="facebook"
                                            value="{{ optional($exhibitorInfo)->facebook }}">
                                    </div>
                                </div>
                                <div class="col-sm-6 mt-3 mt-sm-0">
                                    <div class="input-group input-group-dynamic {{ $cssClass }}">
                                        <label class="form-label">YouTube</label>
                                        <input class="form-control" type="url" name="youtube"
                                            value="{{ optional($exhibitorInfo)->youtube }}">
                                    </div>
                                </div>
                            </div>

                            <div class="button-row d-flex mt-4">
                                <button class="btn bg-gradient-dark ms-auto mb-0" type="submit"
                                    title="Save">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- intl-tel-input JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js"></script>
    <script>
        function updateCharCount() {
            const textarea = document.getElementById('description');
            const charCount = document.getElementById('charCount');
            charCount.textContent = `${textarea.value.length} / 750 characters`;
        }
        document.addEventListener('DOMContentLoaded', updateCharCount);
    </script>

    <script>
        function validateDescriptionLength() {
            const textarea = document.getElementById('description');
            if (textarea.value.length < 300) {
                textarea.setCustomValidity('Company description must be at least 300 characters.');
            } else {
                textarea.setCustomValidity('');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const textarea = document.getElementById('description');
            textarea.addEventListener('input', validateDescriptionLength);
            validateDescriptionLength();
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const phoneInput = document.querySelector("#phone");
            const form = phoneInput.closest('form');
            let iti;

            function initializePhoneInput() {
                iti = window.intlTelInput(phoneInput, {
                    initialCountry: "auto",
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
                    geoIpLookup: function(callback) {
                        fetch('https://ipapi.co/json')
                            .then(res => res.json())
                            .then(data => callback(data.country_code))
                            .catch(() => callback('IN'));
                    },
                    separateDialCode: true,
                    nationalMode: false,
                });

                @if (!empty(optional($exhibitorInfo)->phone))
                    const serverPhone = "{{ optional($exhibitorInfo)->phone }}";
                    if (serverPhone.startsWith('+')) {
                        iti.setNumber(serverPhone);
                    } else {
                        phoneInput.value = serverPhone;
                    }
                @endif
            }

            initializePhoneInput();

            form.addEventListener('submit', function(e) {
                const fullNumber = iti.getNumber();
                if (!fullNumber || !fullNumber.startsWith('+')) {
                    e.preventDefault();
                    alert('Please enter a valid phone number with country code.');
                    phoneInput.focus();
                    return false;
                }
                phoneInput.value = fullNumber;
            });
        });
    </script>
@endsection
