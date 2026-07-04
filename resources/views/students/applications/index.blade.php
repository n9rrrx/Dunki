@extends('layouts.app')

@section('content')

    <!-- HEADER SECTION -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            {{-- Dynamic Title --}}
            @if(Auth::user()->user_type == 'student')
                <h4 class="fw-bold text-body">My Applications</h4>
                <p class="text-muted mb-0">Track the status of your university and visa applications.</p>
            @else
                <h4 class="fw-bold text-dark">All Applications</h4>
                <p class="text-muted mb-0">List of the applications from all students.</p>
            @endif
        </div>
        <div class="col-md-4 text-md-end">
            {{-- ONLY Students see the 'New Application' button --}}
            @if(Auth::user()->user_type == 'student')
                <a href="{{ route('universities.index') }}" class="btn btn-primary shadow-sm">
                    <i class="ri-add-line align-middle me-1"></i> New Application
                </a>
            @endif
        </div>
    </div>

    <!-- APPLICATIONS TABLE -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead class="bg-light">
                    <tr>
                        <th class="ps-4">App ID</th>

                        {{-- ✅ ADVISOR ONLY: Show Student Name Column --}}
                        @if(Auth::user()->user_type != 'student')
                            <th>Applicant</th>
                        @endif

                        <th>University</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($applications as $app)
                        <tr>
                            <td class="ps-4 fw-medium text-primary">
                                #{{ $app->application_number }}
                            </td>

                            {{-- ✅ ADVISOR ONLY: Show Student Details --}}
                            @if(Auth::user()->user_type != 'student')
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-xs bg-primary-subtle rounded-circle me-2 d-flex align-items-center justify-content-center">
                                            <span class="fw-bold text-primary">{{ substr($app->clientProfile->user->name ?? 'U', 0, 1) }}</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fs-13 text-dark">{{ $app->clientProfile->user->name ?? 'Unknown' }}</h6>
                                            <small class="text-muted">{{ $app->clientProfile->user->email ?? '' }}</small>
                                        </div>
                                    </div>
                                </td>
                            @endif

                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs bg-light rounded-circle me-2 d-flex align-items-center justify-content-center">
                                        <i class="ri-bank-line text-muted"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fs-14 fw-semibold text-dark">{{ $app->university_name }}</h6>
                                        <small class="text-muted">{{ $app->destination_country }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ ucwords(str_replace('_', ' ', $app->type)) }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusColor = match($app->status) {
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                        'under_review' => 'info',
                                        'submitted' => 'warning',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusColor }}-subtle text-{{ $statusColor }} border border-{{ $statusColor }}-subtle px-2 py-1">
                                    {{ ucfirst(str_replace('_', ' ', $app->status)) }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                @if(Auth::user()->user_type == 'student')
                                    <a href="{{ route('applications.show', $app->id) }}" class="btn btn-sm btn-soft-primary">
                                        Track <i class="ri-arrow-right-line ms-1"></i>
                                    </a>
                                @else
                                    {{-- ADVISOR LOGIC --}}
                                    @if($app->status == 'submitted')
                                        {{-- Action Required --}}
                                        <a href="{{ route('applications.show', $app->id) }}" class="btn btn-sm btn-primary shadow-sm">
                                            Review <i class="ri-edit-box-line ms-1"></i>
                                        </a>
                                    @else
                                        {{-- Archived / Read Only --}}
                                        <a href="{{ route('applications.show', $app->id) }}" class="btn btn-sm btn-light border">
                                            View <i class="ri-eye-line ms-1"></i>
                                        </a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ Auth::user()->user_type == 'student' ? 6 : 7 }}" class="text-center py-5">
                                <div class="avatar-lg bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                                    <i class="ri-file-search-line fs-1 text-muted"></i>
                                </div>
                                <h5 class="text-muted">No applications found.</h5>
                                @if(Auth::user()->user_type == 'student')
                                    <a href="{{ route('universities.index') }}" class="btn btn-outline-primary btn-sm mt-2">
                                        Browse Universities
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-center">
                {{ $applications->links() }}
            </div>
        </div>
    </div>

@endsection
