@extends('layouts.app')

@section('content')

    <div class="row mb-4 align-items-end">
        <div class="col-md-8">
            <h4 class="fw-bold text-dark">Admin Command Center 🛡️</h4>
            <p class="text-muted mb-0">Overview of all operations, staff assignments, and application progress.</p>
        </div>
        <div class="col-md-4 text-md-end">
            <a href="{{ route('staff.index') }}" class="btn btn-dark shadow-sm">
                <i class="ri-user-settings-line me-1"></i> Manage Staff List
            </a>
        </div>
    </div>

    <!-- 1. KEY METRICS -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm border-start border-4 border-primary h-100">
                <div class="card-body">
                    <p class="text-muted mb-1 text-uppercase fs-11 fw-bold">Total Students</p>
                    <h3 class="mb-0 fw-bold">{{ $totalStudents }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm border-start border-4 border-info h-100">
                <div class="card-body">
                    <p class="text-muted mb-1 text-uppercase fs-11 fw-bold">Active Applications</p>
                    <h3 class="mb-0 fw-bold">{{ $totalApplications }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm border-start border-4 border-success h-100">
                <div class="card-body">
                    <p class="text-muted mb-1 text-uppercase fs-11 fw-bold">Docs Verified</p>
                    <h3 class="mb-0 fw-bold">{{ $totalVerifiedFiles }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm border-start border-4 border-warning h-100">
                <div class="card-body">
                    <p class="text-muted mb-1 text-uppercase fs-11 fw-bold">Tasks Done</p>
                    <h3 class="mb-0 fw-bold">{{ $totalCompletedTasks }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. MASTER APPLICATION TABLE -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="card-title mb-0 fw-bold">Global Application Tracker</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead class="bg-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Student</th>
                        <th>University</th>
                        <th>Status</th>
                        <th>Assigned Staff</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($allApplications as $app)
                        <tr>
                            <td class="ps-4 text-muted">#{{ $app->application_number }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs bg-primary-subtle rounded-circle me-2 d-flex align-items-center justify-content-center">
                                        <span class="fw-bold text-primary">{{ substr($app->clientProfile->user->name ?? 'U', 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fs-13 text-dark">{{ $app->clientProfile->user->name }}</h6>
                                        <small class="text-muted">{{ $app->clientProfile->user->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $app->university_name }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ ucwords(str_replace('_', ' ', $app->status)) }}</span>
                            </td>
                            <td>
                                <!-- Assigned Staff Avatars -->
                                <div class="d-flex align-items-center">
                                    {{-- Advisor --}}
                                    <div class="avatar-xs me-1" title="Advisor: {{ $app->clientProfile->advisor->name ?? 'Unassigned' }}">
                                        <span class="avatar-title rounded-circle {{ $app->clientProfile->advisor ? 'bg-warning-subtle text-warning' : 'bg-light text-muted' }}">
                                            <i class="ri-user-star-line"></i>
                                        </span>
                                    </div>
                                    {{-- Visa --}}
                                    <div class="avatar-xs me-1" title="Visa: {{ $app->clientProfile->visaConsultant->name ?? 'Unassigned' }}">
                                        <span class="avatar-title rounded-circle {{ $app->clientProfile->visaConsultant ? 'bg-info-subtle text-info' : 'bg-light text-muted' }}">
                                            <i class="ri-passport-line"></i>
                                        </span>
                                    </div>
                                    {{-- Travel --}}
                                    <div class="avatar-xs" title="Travel: {{ $app->clientProfile->travelAgent->name ?? 'Unassigned' }}">
                                        <span class="avatar-title rounded-circle {{ $app->clientProfile->travelAgent ? 'bg-purple-subtle text-purple' : 'bg-light text-muted' }}">
                                            <i class="ri-plane-line"></i>
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#assignModal-{{ $app->id }}">
                                    <i class="ri-user-add-line"></i> Assign
                                </button>
                            </td>
                        </tr>

                        <!-- ASSIGN MODAL FOR THIS ROW -->
                        <div class="modal fade" id="assignModal-{{ $app->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <form action="{{ route('admin.assign_staff', $app->clientProfile->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Assign Staff to {{ $app->clientProfile->user->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-start">

                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-uppercase text-muted">Academic Advisor</label>
                                                <select name="advisor_id" class="form-select">
                                                    <option value="">-- Unassigned --</option>
                                                    @foreach($advisors as $staff)
                                                        <option value="{{ $staff->id }}" {{ $app->clientProfile->advisor_id == $staff->id ? 'selected' : '' }}>
                                                            {{ $staff->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-uppercase text-muted">Visa Consultant</label>
                                                <select name="visa_consultant_id" class="form-select">
                                                    <option value="">-- Unassigned --</option>
                                                    @foreach($visaConsultants as $staff)
                                                        <option value="{{ $staff->id }}" {{ $app->clientProfile->visa_consultant_id == $staff->id ? 'selected' : '' }}>
                                                            {{ $staff->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label small fw-bold text-uppercase text-muted">Travel Agent</label>
                                                <select name="travel_agent_id" class="form-select">
                                                    <option value="">-- Unassigned --</option>
                                                    @foreach($travelAgents as $staff)
                                                        <option value="{{ $staff->id }}" {{ $app->clientProfile->travel_agent_id == $staff->id ? 'selected' : '' }}>
                                                            {{ $staff->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary">Save Assignments</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    @empty
                        <tr><td colspan="6" class="text-center py-5 text-muted">No applications found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-top py-3">
            {{ $allApplications->links() }}
        </div>
    </div>

@endsection
