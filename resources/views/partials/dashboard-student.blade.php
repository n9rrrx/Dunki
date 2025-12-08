@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-xxl-8 col-xl-8 col-lg-12">
            <div class="card bg-primary-subtle border-0 h-100">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <h4 class="mb-1 text-primary fw-bold">Welcome Back, {{ Auth::user()->name }}! 👋</h4>
                            <p class="text-muted mb-4">
                                You are <span class="fw-bold text-body">{{ $profileCompletion }}%</span> of the way there.
                                Complete your profile to start applying to universities.
                            </p>

                            <div class="progress mb-3" style="height: 10px;">
                                <div class="progress-bar bg-primary" role="progressbar"
                                     style="width: {{ $profileCompletion }}%"
                                     aria-valuenow="{{ $profileCompletion }}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>

                            {{-- SMART BUTTON LOGIC --}}
                            @php
                                $user = Auth::user();
                                // Check if the basic text fields are done
                                $basicProfileDone = $user->phone && $user->country && $user->location && $user->profile_pic;
                            @endphp

                            @if($profileCompletion < 100)
                                @if(!$basicProfileDone)
                                    {{-- Step 1: Finish Basic Info --}}
                                    <a href="{{ route('profile.edit') }}" class="btn btn-warning btn-sm rounded-pill px-3 shadow-sm">
                                        <i class="ri-user-settings-line align-middle me-1"></i> Complete Basic Info
                                    </a>
                                @else
                                    {{-- Step 2: Upload Documents (The part you were missing!) --}}
                                    <a href="{{ route('files.index') }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
                                        <i class="ri-upload-cloud-2-line align-middle me-1"></i> Upload Documents
                                    </a>
                                @endif
                            @else
                                {{-- Step 3: Apply --}}
                                <a href="{{ route('universities.index') }}" class="btn btn-success btn-sm rounded-pill px-3 shadow-sm">
                                    <i class="ri-search-line align-middle me-1"></i> Find Universities
                                </a>
                            @endif
                        </div>

                        <div class="col-md-5 d-none d-md-block text-center">
                            <img src="{{ asset('assets/images/auth/auth_bg.jpg') }}" class="img-fluid rounded shadow-sm" style="max-height: 140px; opacity: 0.8;" alt="Welcome">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xxl-4 col-xl-4 col-lg-12 mt-4 mt-xl-0">
            <div class="row g-3">
                <div class="col-md-6 col-xl-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body d-flex align-items-center">
                            <div class="avatar-md bg-info-subtle text-info rounded-3 d-flex align-items-center justify-content-center me-3">
                                <i class="ri-file-list-3-line fs-2"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-1">Active Applications</p>
                                <h4 class="mb-0 fw-bold">{{ $applications->count() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xl-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body d-flex align-items-center">
                            <div class="avatar-md bg-warning-subtle text-warning rounded-3 d-flex align-items-center justify-content-center me-3">
                                <i class="ri-task-line fs-2"></i>
                            </div>
                            <div>
                                <p class="text-muted mb-1">Pending Tasks</p>
                                <h4 class="mb-0 fw-bold">{{ $tasks->count() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title mb-0">Recent Applications</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 table-hover">
                            <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Application ID</th>
                                <th>University</th>
                                <th>Status</th>
                                <th>Date Applied</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($applications->take(5) as $app)
                                <tr>
                                    <td class="ps-4 fw-medium text-primary">#{{ $app->application_number }}</td>
                                    <td>{{ $app->university_name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $app->status == 'approved' ? 'success' : ($app->status == 'rejected' ? 'danger' : 'warning') }}-subtle text-{{ $app->status == 'approved' ? 'success' : ($app->status == 'rejected' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($app->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $app->created_at->format('d M, Y') }}</td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('applications.index') }}" class="btn btn-sm btn-light">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="ri-folder-open-line fs-1 d-block mb-2"></i>
                                            No applications found. <a href="{{ route('universities.index') }}">Start applying!</a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
