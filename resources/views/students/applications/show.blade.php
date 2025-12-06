@extends('layouts.app')

@section('content')

    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            @if(Auth::user()->user_type == 'academic_advisor')
                <h4 class="fw-bold mb-1">Review Application <span class="badge bg-warning-subtle text-warning fs-12 align-middle border border-warning-subtle ms-2">Advisor Mode</span></h4>
                <p class="text-muted mb-0">Applicant: <span class="fw-medium text-dark">{{ $application->clientProfile->user->name }}</span> • ID: #{{ $application->application_number }}</p>
            @elseif(Auth::user()->user_type == 'visa_consultant')
                <h4 class="fw-bold mb-1">Visa Filing <span class="badge bg-info-subtle text-info fs-12 align-middle border border-info-subtle ms-2">Consultant Mode</span></h4>
                <p class="text-muted mb-0">Applicant: <span class="fw-medium text-dark">{{ $application->clientProfile->user->name }}</span> • ID: #{{ $application->application_number }}</p>
            @elseif(Auth::user()->user_type == 'travel_agent')
                <h4 class="fw-bold mb-1">Travel Logistics <span class="badge bg-purple-subtle text-purple fs-12 align-middle border border-purple-subtle ms-2">Agent Mode</span></h4>
                <p class="text-muted mb-0">Applicant: <span class="fw-medium text-body">{{ $application->clientProfile->user->name }}</span> • ID: #{{ $application->application_number }}</p>
            @else
                <h4 class="fw-bold mb-1">Application Details</h4>
                <p class="text-muted mb-0">ID: <span class="fw-medium text-dark">#{{ $application->application_number }}</span></p>
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
                        <span class="badge bg-light text-body border">Verify these files</span>
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
                    <div class="card-header bg-white py-3"><h6 class="card-title mb-0 text-dark fw-bold">Decision Console</h6></div>
                    <div class="card-body">
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
                @if(!in_array($application->status, ['visa_granted', 'visa_rejected', 'travel_booking', 'travel_booked']))
                    <div class="card border-0 shadow-sm mb-4 border-top border-4 border-dark">
                        <div class="card-header bg-white py-3"><h6 class="card-title mb-0 text-dark fw-bold">Visa Filing Console</h6></div>
                        <div class="card-body">
                            @if($application->status == 'visa_processing')
                                <form action="{{ route('applications.updateStatus', $application->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="visa_submitted">
                                    <button class="btn btn-dark w-100"><i class="ri-mail-send-line me-1"></i> Mark as Submitted</button>
                                </form>
                            @elseif($application->status == 'visa_submitted')
                                <div class="d-grid gap-2">
                                    <form action="{{ route('applications.updateStatus', $application->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="visa_granted">
                                        <button class="btn btn-success w-100"><i class="ri-passport-line me-1"></i> Visa Granted</button>
                                    </form>
                                    <button class="btn btn-outline-danger" type="button" data-bs-toggle="collapse" data-bs-target="#visaReject"><i class="ri-close-circle-line me-1"></i> Visa Rejected</button>
                                    <div class="collapse" id="visaReject">
                                        <form action="{{ route('applications.updateStatus', $application->id) }}" method="POST" class="mt-2">
                                            @csrf
                                            <input type="hidden" name="status" value="visa_rejected">
                                            <textarea name="reason" class="form-control mb-2" rows="2" placeholder="Reason..." required></textarea>
                                            <button class="btn btn-danger btn-sm w-100">Confirm</button>
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
                        <div class="card-header bg-white py-3"><h6 class="card-title mb-0 text-dark fw-bold">Travel Booking</h6></div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">Arrange flights and accommodation for the student.</p>

                            <form action="{{ route('applications.updateStatus', $application->id) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="travel_booked">

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-uppercase">Flight Details / Note</label>
                                    <textarea name="reason" class="form-control" rows="3" placeholder="e.g. Flight EK602 booked for 12th Aug..." required></textarea>
                                </div>

                                <button class="btn w-100 text-white" style="background-color: #6f42c1;">
                                    <i class="ri-plane-fill me-1"></i> Confirm Booking
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

                $iconClass = match($application->status) {
                    'approved', 'visa_granted', 'travel_booked' => 'ri-check-double-line',
                    'rejected', 'visa_rejected' => 'ri-close-circle-line',
                    'visa_submitted', 'travel_booking' => 'ri-send-plane-fill',
                    'visa_processing' => 'ri-file-settings-line',
                    default => 'ri-loader-4-line spin'
                };
            @endphp

            <div class="card border-0 shadow-sm mb-4 {{ $statusColor }}">
                <div class="card-body p-4">
                    <h6 class="text-white-50 text-uppercase fs-11 fw-bold mb-2">Current Status</h6>
                    <div class="d-flex align-items-center mb-3">
                        <i class="{{ $iconClass }} fs-3 me-2"></i>
                        <h2 class="text-white mb-0 fw-bold">{{ ucwords(str_replace('_', ' ', $application->status)) }}</h2>
                    </div>

                    @if($application->status == 'submitted')
                        <p class="mb-0 small text-white-50">Pending Review. Waiting for advisor.</p>
                    @elseif($application->status == 'approved')
                        <p class="mb-0 small text-white-50">Approved! Moved to Visa Processing.</p>
                    @elseif($application->status == 'visa_processing')
                        <p class="mb-0 small text-white-50">Visa Consultant is preparing your file.</p>
                    @elseif($application->status == 'visa_submitted')
                        <p class="mb-0 small text-white-50">Application submitted to the Embassy.</p>
                    @elseif($application->status == 'visa_granted')
                        <p class="mb-0 small text-white-50">Congratulations! Visa Approved.</p>
                    @elseif($application->status == 'travel_booking')
                        <p class="mb-0 small text-white-50">Travel Agent is arranging logistics.</p>
                    @elseif($application->status == 'travel_booked')
                        <p class="mb-0 small text-white-50">Travel booked! Ready to fly.</p>

                    @elseif(in_array($application->status, ['rejected', 'visa_rejected']))
                        <p class="mb-0 small text-white-50">Application Returned. Please check the notes below.</p>

                        @if(Auth::user()->user_type == 'student')
                            <div class="mt-3 pt-3 border-top border-white-50">
                                <a href="{{ route('applications.edit', $application->id) }}" class="btn btn-light btn-sm w-100 fw-bold text-danger shadow-sm">
                                    <i class="ri-edit-2-line me-1"></i> Edit & Resubmit Application
                                </a>
                            </div>
                        @endif
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

    <style>
        .spin { animation: spin 2s linear infinite; }
        @keyframes spin { 100% { transform: rotate(360deg); } }
    </style>

@endsection
