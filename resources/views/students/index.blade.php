@extends('layouts.app')

@section('content')

    <!-- PAGE HEADER -->
    <div class="hstack flex-wrap gap-3 mb-5">
        <div class="flex-grow-1">
            <h4 class="mb-1 fw-bold text-dark">Student Management</h4>
            <nav>
                <ol class="breadcrumb breadcrumb-arrow mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Students</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex my-xl-auto align-items-center flex-wrap flex-shrink-0">
            <a href="{{ route('students.create') }}" class="btn btn-primary shadow-sm">
                <i class="ri-user-add-line align-middle me-1"></i> Add Manual Student
            </a>
        </div>
    </div>

    <!-- STUDENTS TABLE -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Registered Students</h5>

                <form action="{{ route('students.index') }}" method="GET" class="d-flex gap-2">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search name or email..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-sm btn-light border"><i class="ri-search-line"></i></button>
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead class="bg-light">
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Student Name</th>
                        <th>Contact</th>
                        <th>Assigned Staff</th> {{-- ✅ Added Column --}}
                        <th>Status</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td class="ps-4 fw-medium text-muted">#{{ $student->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs bg-light rounded-circle me-2 d-flex align-items-center justify-content-center">
                                        @if($student->profile_pic)
                                            <img src="{{ asset('storage/'.$student->profile_pic) }}" class="img-fluid rounded-circle" style="width:100%; height:100%; object-fit:cover;">
                                        @else
                                            <span class="fw-bold text-primary">{{ substr($student->name, 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fs-13 text-dark">{{ $student->name }}</h6>
                                        <small class="text-muted">Joined {{ $student->created_at->format('M Y') }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="text-dark fs-13">{{ $student->email }}</span>
                                    <span class="text-muted fs-11">{{ $student->phone ?? '-' }}</span>
                                </div>
                            </td>
                            <td>
                                {{-- ✅ Assigned Staff Icons --}}
                                @if($student->clientProfile)
                                    <div class="d-flex align-items-center gap-1">
                                        {{-- Advisor --}}
                                        <div class="avatar-xs" title="Advisor: {{ $student->clientProfile->advisor->name ?? 'Unassigned' }}">
                                                <span class="avatar-title rounded-circle {{ $student->clientProfile->advisor ? 'bg-warning-subtle text-warning' : 'bg-light text-muted' }} fs-10">
                                                    <i class="ri-user-star-line"></i>
                                                </span>
                                        </div>
                                        {{-- Visa --}}
                                        <div class="avatar-xs" title="Visa: {{ $student->clientProfile->visaConsultant->name ?? 'Unassigned' }}">
                                                <span class="avatar-title rounded-circle {{ $student->clientProfile->visaConsultant ? 'bg-info-subtle text-info' : 'bg-light text-muted' }} fs-10">
                                                    <i class="ri-passport-line"></i>
                                                </span>
                                        </div>
                                        {{-- Travel --}}
                                        <div class="avatar-xs" title="Travel: {{ $student->clientProfile->travelAgent->name ?? 'Unassigned' }}">
                                                <span class="avatar-title rounded-circle {{ $student->clientProfile->travelAgent ? 'bg-purple-subtle text-purple' : 'bg-light text-muted' }} fs-10">
                                                    <i class="ri-plane-line"></i>
                                                </span>
                                        </div>
                                    </div>
                                @else
                                    <span class="badge bg-light text-muted border">No Profile</span>
                                @endif
                            </td>
                            <td>
                                @if($student->is_active)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Active</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-flex gap-2 justify-content-end">

                                    {{-- ✅ Assign Button (Only if profile exists) --}}
                                    @if($student->clientProfile)
                                        <button class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#assignModal-{{ $student->id }}"
                                                title="Assign Staff">
                                            <i class="ri-user-settings-line"></i>
                                        </button>
                                    @endif

                                    <a href="{{ route('students.edit', $student->id) }}" class="btn btn-sm btn-soft-primary" title="Edit">
                                        <i class="ri-pencil-line"></i>
                                    </a>
                                    <form action="{{ route('students.destroy', $student->id) }}" method="POST" onsubmit="return confirm('Delete user?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-soft-danger" title="Delete">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        {{-- ✅ ASSIGN STAFF MODAL --}}
                        @if($student->clientProfile)
                            <div class="modal fade" id="assignModal-{{ $student->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.assign_staff', $student->clientProfile->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Assign Staff to {{ $student->name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-start">

                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-muted">Academic Advisor</label>
                                                    <select name="advisor_id" class="form-select">
                                                        <option value="">-- Unassigned --</option>
                                                        @foreach($advisors as $staff)
                                                            <option value="{{ $staff->id }}" {{ $student->clientProfile->advisor_id == $staff->id ? 'selected' : '' }}>
                                                                {{ $staff->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-muted">Visa Consultant</label>
                                                    <select name="visa_consultant_id" class="form-select">
                                                        <option value="">-- Unassigned --</option>
                                                        @foreach($visaConsultants as $staff)
                                                            <option value="{{ $staff->id }}" {{ $student->clientProfile->visa_consultant_id == $staff->id ? 'selected' : '' }}>
                                                                {{ $staff->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-muted">Travel Agent</label>
                                                    <select name="travel_agent_id" class="form-select">
                                                        <option value="">-- Unassigned --</option>
                                                        @foreach($travelAgents as $staff)
                                                            <option value="{{ $staff->id }}" {{ $student->clientProfile->travel_agent_id == $staff->id ? 'selected' : '' }}>
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
                        @endif

                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="ri-user-unfollow-line fs-1 d-block mb-2"></i>
                                No students found.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-center">
                {{ $students->links() }}
            </div>
        </div>
    </div>

@endsection
