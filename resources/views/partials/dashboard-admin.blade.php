@extends('layouts.app')

@section('content')

    <div class="row mb-4">
        <div class="col-12">
            <h4 class="fw-bold text-body">Admin Dashboard</h4>
            <p class="text-muted">Welcome back. Here is what is happening across the platform today.</p>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm border-start border-4 border-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Total Students</p>
                            <h3 class="mb-0 fw-bold">{{ $totalStudents }}</h3>
                        </div>
                        <div class="avatar-sm bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center">
                            <i class="ri-user-smile-line fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm border-start border-4 border-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Applications</p>
                            <h3 class="mb-0 fw-bold">{{ $totalApplications }}</h3>
                        </div>
                        <div class="avatar-sm bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center">
                            <i class="ri-file-list-3-line fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm border-start border-4 border-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Total Revenue</p>
                            <h3 class="mb-0 fw-bold">${{ number_format($totalFees) }}</h3>
                        </div>
                        <div class="avatar-sm bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center">
                            <i class="ri-money-dollar-circle-line fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm border-start border-4 border-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Tasks Completed</p>
                            <h3 class="mb-0 fw-bold">{{ $totalCompletedTasks }}</h3>
                        </div>
                        <div class="avatar-sm bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center">
                            <i class="ri-checkbox-circle-line fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">Recent Applications</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle table-nowrap mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Student</th>
                                <th>University</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($recentApplications as $app)
                                <tr>
                                    <td>#{{ $app->id }}</td>
                                    <td>{{ $app->clientProfile->user->name ?? 'Unknown' }}</td>
                                    <td>{{ $app->university_name }}</td>
                                    <td><span class="badge bg-primary-subtle text-primary">{{ ucfirst($app->status) }}</span></td>
                                    <td><button class="btn btn-sm btn-light">View</button></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">No recent applications.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h5 class="card-title mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <button class="btn btn-outline-primary text-start">
                            <i class="ri-user-add-line me-2"></i> Add New Staff Member
                        </button>
                        <button class="btn btn-outline-dark text-start">
                            <i class="ri-building-2-line me-2"></i> Import Universities (API)
                        </button>
                        <button class="btn btn-outline-success text-start">
                            <i class="ri-file-excel-line me-2"></i> Export Reports
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
