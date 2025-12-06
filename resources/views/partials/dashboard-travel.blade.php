@extends('layouts.app')

@section('content')

    <div class="row mb-4 align-items-end">
        <div class="col-md-8">
            <h4 class="fw-bold text-body">Travel & Logistics ✈️</h4>
            <p class="text-muted mb-0">Coordinate flights, accommodation, and airport pickups.</p>
        </div>
    </div>

    <!-- 1. READY FOR TRAVEL (New Visa Grants) -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-info-subtle py-3">
            <h6 class="card-title mb-0 text-info fw-bold">
                <i class="ri-passport-line me-1"></i> Ready for Booking
            </h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead class="bg-light">
                    <tr>
                        <th class="ps-4">App ID</th>
                        <th>Student</th>
                        <th>Destination</th>
                        <th>Visa Date</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($readyForTravel as $app)
                        <tr>
                            <td class="ps-4 fw-medium">#{{ $app->application_number }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-xs bg-success-subtle rounded-circle me-2 d-flex align-items-center justify-content-center">
                                        <span class="fw-bold text-primary">{{ substr($app->clientProfile->user->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fs-13 text-body">{{ $app->clientProfile->user->name }}</h6>
                                        <small class="text-muted">{{ $app->clientProfile->user->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-danger text-body border">
                                    <i class="ri-map-pin-line me-1"></i> {{ $app->destination_country }}
                                </span>
                            </td>
                            <td class="text-muted">{{ $app->updated_at->format('d M, Y') }}</td>
                            <td class="text-end pe-4">
                                <form action="{{ route('travel.start', $app->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary text-white shadow-sm">
                                        Start Booking <i class="ri-plane-takeoff-line ms-1"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="avatar-lg bg-light rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                                    <i class="ri-flight-takeoff-line fs-1 text-muted"></i>
                                </div>
                                <h5 class="text-muted">No students ready to fly.</h5>
                                <p class="text-muted small">Wait for Visa Consultants to grant visas.</p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 2. BOOKING IN PROGRESS -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="card-title mb-0 text-dark fw-bold">Active Bookings</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Student</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($bookingInProgress as $case)
                        <tr>
                            <td class="ps-4">
                                <span class="fw-semibold text-dark">{{ $case->clientProfile->user->name }}</span>
                            </td>
                            <td>
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                    Booking in Progress
                                </span>
                            </td>
                            <td class="text-muted small">{{ $case->updated_at->diffForHumans() }}</td>
                            <td class="text-end pe-4">
                                <a href="{{ route('applications.show', $case->id) }}" class="btn btn-sm btn-primary shadow-sm">
                                    Manage Tickets <i class="ri-arrow-right-line ms-1"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">Queue is empty.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
