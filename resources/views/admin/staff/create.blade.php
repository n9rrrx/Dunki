@extends('layouts.app')

@section('content')

    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="d-flex align-items-center mb-4">
                <a href="{{ route('staff.index') }}" class="btn btn-light border shadow-sm me-3"><i class="ri-arrow-left-line"></i> Back</a>
                <h4 class="fw-bold mb-0">Add New Employee 👔</h4>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="card-title mb-0">Staff Details</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('staff.store') }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-bold">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Sarah Smith" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" placeholder="staff@dunki.com" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Phone (Optional)</label>
                                <input type="text" name="phone" class="form-control" placeholder="+1 234...">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Role / Job Title <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required>
                                <option value="" disabled selected>Select Role...</option>
                                <option value="academic_advisor">🎓 Academic Advisor</option>
                                <option value="visa_consultant">🛂 Visa Consultant</option>
                                <option value="travel_agent">✈️ Travel Agent</option>
                            </select>
                            <div class="form-text text-muted">This determines which dashboard permissions they will have.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Set initial password" required>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="reset" class="btn btn-light">Reset</button>
                            <button type="submit" class="btn btn-primary px-4">Create Account</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
