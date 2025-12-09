@extends('layouts.app')

@section('content')

    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            @if(Auth::user()->user_type == 'academic_advisor')
                <h4 class="fw-bold mb-1">Review Application <span class="badge bg-warning-subtle text-warning fs-12 align-middle border border-warning-subtle ms-2">Advisor Mode</span></h4>
                <p class="text-muted mb-0">Applicant: <span class="fw-medium text-body">{{ $application->clientProfile->user->name }}</span> • ID: #{{ $application->application_number }}</p>
            @elseif(Auth::user()->user_type == 'visa_consultant')
                <h4 class="fw-bold mb-1">Visa Filing <span class="badge bg-info-subtle text-info fs-12 align-middle border border-info-subtle ms-2">Consultant Mode</span></h4>
                <p class="text-muted mb-0">Applicant: <span class="fw-medium text-body">{{ $application->clientProfile->user->name }}</span> • ID: #{{ $application->application_number }}</p>
            @elseif(Auth::user()->user_type == 'travel_agent')
                <h4 class="fw-bold mb-1">Travel Logistics <span class="badge bg-purple-subtle text-purple fs-12 align-middle border border-purple-subtle text-body ms-2">Agent Mode</span></h4>
                <p class="text-muted mb-0">Applicant: <span class="fw-medium text-dark">{{ $application->clientProfile->user->name }}</span> • ID: #{{ $application->application_number }}</p>
            @else
                <h4 class="fw-bold mb-1">Application Details</h4>
                <p class="text-muted mb-0">ID: <span class="fw-medium text-body">#{{ $application->application_number }}</span></p>
            @endif
        </div>
        <div>
            {{-- Smart Back Button --}}
            @if(Auth::user()->user_type == 'academic_advisor')
                <a href="{{ route('academic.dashboard') }}" class="btn btn-white border shadow-sm"><i class="ri-arrow-left-line me-1"></i> Back to Queue</a>
            @elseif(Auth::user()->user_type == 'visa_consultant')
                <a href="{{ route('consultant.dashboard') }}" class="btn btn-white border shadow-sm"><i class="ri-arrow-left-line me-1"></i> Back to Visa Center</a>
            @elseif(Auth::user()->user_type == 'travel_agent')
                <a href="{{ route('travel.dashboard') }}" class="btn btn-white border shadow-sm"><i class="ri-arrow-left-line me-1"></i> Back to Logistics</a>
            @else
                <a href="{{ route('applications.index') }}" class="btn btn-white border shadow-sm"><i class="ri-arrow-left-line me-1"></i> Back to List</a>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- LEFT COLUMN -->
        <div class="col-lg-8">

            {{-- ✈️ TRAVEL WALLET (Shows up when tickets are booked) --}}
            @if($application->status == 'travel_booked')
                <div class="card border-0 shadow-sm mb-4 border-top border-4 border-success">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0 fw-bold text-success">
                            <i class="ri-plane-line me-1"></i> Travel Documents (Confirmed)
                        </h6>
                        <span class="badge bg-success">Ready to Fly</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Document</th>
                                    <th>Type</th>
                                    <th class="text-end pe-4">Download</th>
                                </tr>
                                </thead>
                                <tbody>
                                {{-- Filter only Travel Docs --}}
                                @php
                                    $travelDocs = \App\Models\File::where('application_id', $application->id)
                                        ->whereIn('file_type', ['flight_ticket', 'hotel_voucher', 'travel_insurance'])
                                        ->get();
                                @endphp

                                @forelse($travelDocs as $doc)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-light rounded d-flex align-items-center justify-content-center me-2">
                                                    <i class="ri-file-pdf-line fs-5 text-danger"></i>
                                                </div>
                                                <span class="fw-medium text-body">{{ $doc->original_name }}</span>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-light text-body border">{{ ucwords(str_replace('_', ' ', $doc->file_type)) }}</span></td>
                                        <td class="text-end pe-4">
                                            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="btn btn-sm btn-dark">
                                                <i class="ri-download-line me-1"></i> Download
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center py-3">No tickets found.</td></tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ✈️ STUDENT: TRAVEL PREFERENCES FORM --}}
            {{-- Shows up for Student when Visa is Granted --}}
            @if(Auth::user()->user_type == 'student' && in_array($application->status, ['visa_granted', 'travel_booking']))

                @php
                    $prefs = $application->travel_preferences;
                    if (is_string($prefs)) {
                        $prefs = json_decode($prefs, true);
                    }
                @endphp

                <div class="card border-0 shadow-sm mb-4 border-top border-4 border-purple">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="card-title mb-0 fw-bold text-purple"><i class="ri-flight-takeoff-line me-1"></i> Travel Preferences</h6>
                    </div>
                    <div class="card-body p-4">
                        @if($prefs)
                            <div class="alert alert-purple bg-purple-subtle border-0 text-purple mb-0">
                                <strong><i class="ri-check-double-line me-1"></i> Request Sent!</strong><br>
                                You requested flight on <strong>{{ $prefs['date'] ?? 'N/A' }}</strong> from <strong>{{ $prefs['city'] ?? 'N/A' }}</strong>.
                                <br><small>Agent Note: {{ $prefs['notes'] ?? 'None' }}</small>
                            </div>
                        @else
                            <p class="text-muted small mb-3">
                                Congratulations on your Visa! Please tell us when and how you would like to fly so we can book your ticket.
                            </p>
                            <form action="{{ route('applications.travelPreferences', $application->id) }}" method="POST">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-uppercase">Preferred Date</label>
                                        <input type="date" name="travel_date" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-uppercase">Departure City</label>
                                        <input type="text" name="departure_city" class="form-control" placeholder="e.g. Lahore" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-uppercase">Airline / Special Requests</label>
                                        <textarea name="notes" class="form-control" rows="2" placeholder="e.g. Prefer Emirates, Vegetarian Meal..."></textarea>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-purple text-white w-100" style="background-color: #6f42c1;">
                                            <i class="ri-send-plane-fill me-1"></i> Submit to Travel Agent
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            @endif

            {{-- STAFF VIEW: Applicant Info --}}
            @if(Auth::user()->user_type != 'student')
                <div class="card border-0 shadow-sm mb-4 border-top border-4 border-primary">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="card-title mb-0 fw-bold text-primary"><i class="ri-user-search-line me-1"></i> Applicant Profile</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <small class="text-muted text-uppercase fw-bold fs-11">Full Name</small>
                                <p class="fw-bold mb-0 text-body fs-15">{{ $application->clientProfile->user->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted text-uppercase fw-bold fs-11">Email Address</small>
                                <p class="fw-medium mb-0">{{ $application->clientProfile->user->email ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted text-uppercase fw-bold fs-11">Phone Number</small>
                                <p class="fw-medium mb-0">{{ $application->clientProfile->user->phone ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted text-uppercase fw-bold fs-11">Citizenship</small>
                                <p class="fw-medium mb-0">{{ $application->clientProfile->user->country ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- University Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start">
                        <div class="avatar-md bg-light rounded d-flex align-items-center justify-content-center me-3">
                            <i class="ri-bank-line fs-2 text-muted"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="fw-bold mb-1">{{ $application->university_name }}</h5>
                            <p class="text-muted mb-2"><i class="ri-map-pin-line me-1"></i> {{ $application->destination_country }}</p>

                            <div class="hstack gap-3 mt-3">
                                <div class="border-end pe-3">
                                    <small class="text-muted d-block">Course</small>
                                    <span class="fw-medium text-body">{{ $application->course_name ?? 'General Admission' }}</span>
                                </div>
                                <div class="border-end pe-3">
                                    <small class="text-muted d-block">Intake</small>
                                    <span class="fw-medium text-body">{{ $application->intake ?? 'Fall 2025' }}</span>
                                </div>
                                <div>
                                    <small class="text-muted d-block">Type</small>
                                    <span class="fw-medium text-body">{{ ucwords(str_replace('_', ' ', $application->type)) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents Review -->
            <div class="card border-0 shadow-sm mb-4 border-top border-4 border-primary">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="card-title mb-0 fw-bold text-primary">
                        <i class="ri-file-list-3-line me-1"></i> Attached Documents
                    </h6>
                    @if(Auth::user()->user_type != 'student')
                        <span class="badge bg-light text-dark border">Verify these files</span>
                    @endif
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                            <tr>
                                <th class="ps-4">File Name</th>
                                <th>Type</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $files = \App\Models\File::where('uploaded_by', $application->clientProfile->user_id)->get();
                            @endphp

                            @forelse($files as $file)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <i class="ri-file-text-line fs-4 text-primary me-2"></i>
                                            <span class="fw-medium">{{ $file->original_name }}</span>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border">{{ strtoupper($file->file_type) }}</span></td>
                                    <td class="text-end pe-4">
                                        <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank" class="btn btn-sm btn-soft-primary">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">No documents found.</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN -->
        <div class="col-lg-4">

            {{-- 🎓 ADVISOR ACTIONS --}}
            @if(Auth::user()->user_type == 'academic_advisor' && $application->status == 'submitted')
                <div class="card border-0 shadow-sm mb-4 border-top border-4 border-info">
                    <div class="card-header bg-white py-3">
                        <h6 class="card-title mb-0 text-dark fw-bold">Decision Console</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info fs-12 mb-3">
                            <i class="ri-information-line me-1"></i> Please review all documents before making a decision.
                        </div>
                        <div class="d-grid gap-2">
                            <form action="{{ route('applications.updateStatus', $application->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="approved">
                                <button class="btn btn-success w-100"><i class="ri-check-double-line me-1"></i> Approve & Process</button>
                            </form>
                            <button class="btn btn-outline-danger" type="button" data-bs-toggle="collapse" data-bs-target="#rejectReason"><i class="ri-close-line me-1"></i> Reject</button>
                            <div class="collapse mt-2" id="rejectReason">
                                <form action="{{ route('applications.updateStatus', $application->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="rejected">
                                    <textarea name="reason" class="form-control mb-2" rows="3" placeholder="Reason..." required></textarea>
                                    <button class="btn btn-danger btn-sm w-100">Confirm Rejection</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ✈️ VISA CONSULTANT ACTIONS --}}
            @if(Auth::user()->user_type == 'visa_consultant')
                @if(!in_array($application->status, ['visa_granted', 'visa_rejected']))
                    <div class="card border-0 shadow-sm mb-4 border-top border-4 border-dark">
                        <div class="card-header bg-white py-3"><h6 class="card-title mb-0 text-dark fw-bold">Visa Filing Console</h6></div>
                        <div class="card-body">

                            @if($application->status == 'visa_processing')
                                <div class="text-center mb-3">
                                    <div class="avatar-sm bg-light rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center">
                                        <i class="ri-file-paper-2-line fs-5 text-dark"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1">Prepare Filing</h6>
                                    <p class="small text-muted">Collect docs and submit to embassy.</p>
                                </div>
                                <form action="{{ route('applications.updateStatus', $application->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="visa_submitted">
                                    <button class="btn btn-dark w-100"><i class="ri-mail-send-line me-1"></i> Mark as Submitted</button>
                                </form>

                            @elseif($application->status == 'visa_submitted')
                                <div class="text-center mb-3">
                                    <div class="avatar-sm bg-info-subtle rounded-circle mx-auto mb-2 d-flex align-items-center justify-content-center">
                                        <i class="ri-time-line fs-5 text-info"></i>
                                    </div>
                                    <h6 class="fw-bold mb-1 text-info">Under Embassy Review</h6>
                                    <p class="small text-muted">Waiting for final decision.</p>
                                </div>
                                <div class="d-grid gap-2">
                                    <form action="{{ route('applications.updateStatus', $application->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="visa_granted">
                                        <button class="btn btn-success w-100"><i class="ri-passport-line me-1"></i> Visa Granted</button>
                                    </form>

                                    <button class="btn btn-outline-danger" type="button" data-bs-toggle="collapse" data-bs-target="#visaReject">
                                        <i class="ri-close-circle-line me-1"></i> Visa Rejected</button>

                                    <div class="collapse" id="visaReject">
                                        <form action="{{ route('applications.updateStatus', $application->id) }}" method="POST" class="mt-2">
                                            @csrf
                                            <input type="hidden" name="status" value="visa_rejected">
                                            <textarea name="reason" class="form-control mb-2" rows="2" placeholder="Reason from Embassy..." required></textarea>
                                            <button class="btn btn-danger btn-sm w-100">Confirm Rejection</button>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endif

            {{-- 🌍 TRAVEL AGENT ACTIONS --}}
            @if(Auth::user()->user_type == 'travel_agent')
                @if($application->status == 'travel_booking')
                    <div class="card border-0 shadow-sm mb-4 border-top border-4 border-purple">
                        <div class="card-header bg-white py-3"><h6 class="card-title mb-0 text-dark fw-bold">Booking Console</h6></div>
                        <div class="card-body">

                            {{-- ✅ SHOW STUDENT PREFERENCES HERE --}}
                            @php
                                $prefs = $application->travel_preferences;
                                if (is_string($prefs)) {
                                    $prefs = json_decode($prefs, true);
                                }
                            @endphp

                            @if($prefs)
                                <div class="alert alert-info bg-info-subtle border-0 text-info mb-3">
                                    <h6 class="fw-bold mb-1"><i class="ri-user-voice-line me-1"></i> Student Requested:</h6>
                                    <ul class="mb-0 ps-3 small">
                                        <li><strong>Date:</strong> {{ $prefs['date'] ?? 'Any' }}</li>
                                        <li><strong>City:</strong> {{ $prefs['city'] ?? 'Any' }}</li>
                                        <li><strong>Airline:</strong> {{ $prefs['airline'] ?? 'Any' }}</li>
                                        <li><strong>Notes:</strong> {{ $prefs['notes'] ?? 'None' }}</li>
                                    </ul>
                                </div>
                            @else
                                <div class="alert alert-warning small mb-3">
                                    Student has not submitted specific preferences yet.
                                </div>
                            @endif

                            <p class="text-muted small mb-3">Upload confirmed tickets to finalize this case.</p>

                            {{-- UPLOAD FORM --}}
                            <form action="{{ route('travel.upload', $application->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Flight Ticket (PDF)</label>
                                    <input type="file" name="flight_ticket" class="form-control" accept="application/pdf" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Hotel Voucher (Optional)</label>
                                    <input type="file" name="hotel_voucher" class="form-control" accept="application/pdf">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold">Note for Student</label>
                                    <textarea name="notes" class="form-control" rows="2" placeholder="e.g. Flight EK602 confirmed."></textarea>
                                </div>
                                <button type="submit" class="btn btn-purple w-100 text-white" style="background-color: #6f42c1;">
                                    <i class="ri-check-double-line me-1"></i> Confirm & Upload
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            @endif

            {{-- DYNAMIC STATUS BANNER --}}
            @php
                $statusColor = match($application->status) {
                    'approved', 'visa_granted', 'travel_booked' => 'bg-success text-white',
                    'rejected', 'visa_rejected' => 'bg-danger text-white',
                    'visa_submitted', 'travel_booking' => 'bg-info text-white',
                    'visa_processing' => 'bg-dark text-white',
                    default => 'bg-primary text-white'
                };
            @endphp

            <div class="card border-0 shadow-sm mb-4 {{ $statusColor }}">
                <div class="card-body p-4">
                    <h6 class="text-white-50 text-uppercase fs-11 fw-bold mb-2">Current Status</h6>
                    <h2 class="text-white mb-0 fw-bold">{{ ucwords(str_replace('_', ' ', $application->status)) }}</h2>
                    <p class="mb-0 small text-white-50 mt-2">
                        @if($application->status == 'travel_booking')
                            Travel Agent is currently booking your tickets.
                        @elseif($application->status == 'travel_booked')
                            All Set! Travel arrangements have been confirmed.
                        @else
                            {{ $application->notes ?? 'Processing...' }}
                        @endif
                    </p>

                    @if(Auth::user()->user_type == 'student' && in_array($application->status, ['rejected', 'visa_rejected']))
                        <div class="mt-3 pt-3 border-top border-white-50">
                            <a href="{{ route('applications.edit', $application->id) }}" class="btn btn-light btn-sm w-100 fw-bold text-danger text-body shadow-sm">
                                <i class="ri-edit-2-line me-1"></i> Edit & Resubmit Application
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Feedback (If Rejected or Notes exist) -->
            @if($application->notes)
                <div class="card border-0 shadow-sm mb-4 border-start border-4 border-danger">
                    <div class="card-body">
                        <h6 class="fw-bold text-danger mb-2">Feedback / Notes</h6>
                        <p class="text-muted mb-0">{{ $application->notes }}</p>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection
