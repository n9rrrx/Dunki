@extends('layouts.app')

@section('content')

    <!-- PAGE HEADER -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h4 class="fw-bold text-dark">Staff Management 👔</h4>
            <p class="text-muted mb-0">Manage your team of Advisors, Consultants, and Agents.</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('staff.create') }}" class="btn btn-primary shadow-sm">
                <i class="ri-user-add-line me-1"></i> Add New Staff
            </a>
        </div>
    </div>

    <!-- STAFF TABLE -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Name</th>
                        <th>Role</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($staff as $member)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-light rounded-circle me-3 d-flex align-items-center justify-content-center">
                                        <span class="fw-bold text-primary">{{ substr($member->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ $member->name }}</h6>
                                        <small class="text-muted">Joined {{ $member->created_at->format('M Y') }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $color = match($member->user_type) {
                                        'academic_advisor' => 'warning',
                                        'visa_consultant' => 'info',
                                        'travel_agent' => 'purple',
                                        default => 'secondary'
                                    };
                                    // Handle custom purple class if bootstrap doesn't have it
                                    $badgeClass = $color == 'purple' ? 'bg-primary-subtle text-primary' : "bg-{$color}-subtle text-{$color}";
                                @endphp
                                <span class="badge {{ $badgeClass }} border border-{{ $color }}-subtle">
                                        {{ ucwords(str_replace('_', ' ', $member->user_type)) }}
                                    </span>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span>{{ $member->email }}</span>
                                    <small class="text-muted">{{ $member->phone ?? '-' }}</small>
                                </div>
                            </td>
                            <td><span class="badge bg-success-subtle text-success">Active</span></td>
                            <td class="text-end pe-4">
                                <form action="{{ route('staff.destroy', $member->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this staff member?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-soft-danger"><i class="ri-delete-bin-line"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-5 text-muted">No staff found. Click "Add New Staff" to create one.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-top py-3">
            {{ $staff->links() }}
        </div>
    </div>

@endsection
