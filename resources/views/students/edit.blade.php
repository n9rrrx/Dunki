@extends('layouts.app')

@section('content')

    <!-- PAGE HEADER -->
    <div class="hstack flex-wrap gap-3 mb-5">
        <div class="flex-grow-1">
            <h4 class="mb-1 fw-bold text-body">Edit Profile</h4>
            <nav>
                <ol class="breadcrumb breadcrumb-arrow mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('profile.show') }}">Profile</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- EDIT FORM CARD -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Update Information</h5>
                </div>
                <div class="card-body p-4">

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Name & Email -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                                @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">Email Address</label>
                                <input type="email" class="form-control bg-light" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
                                @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Password (Optional) -->
                        <div class="mb-4">
                            <label for="password" class="form-label fw-semibold">New Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep current password">
                            @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <hr class="border-dashed my-4">
                        <h6 class="fw-bold mb-3">Contact Details</h6>

                        <!-- Phone & Country -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold">Phone Number</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}">
                                @error('phone') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="country" class="form-label fw-semibold">Citizenship / Country</label>
                                <input type="text" class="form-control" id="country" name="country" value="{{ old('country', auth()->user()->country) }}">
                                @error('country') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="mb-4">
                            <label for="location" class="form-label fw-semibold">Current City / Location</label>
                            <input type="text" class="form-control" id="location" name="location" value="{{ old('location', auth()->user()->location) }}">
                            @error('location') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <hr class="border-dashed my-4">

                        <!-- Profile Picture -->
                        <div class="mb-4">
                            <label for="profile_pic" class="form-label fw-semibold">Profile Picture</label>
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-md">
                                    <img src="{{ auth()->user()->profile_pic ? asset('storage/' . auth()->user()->profile_pic) : asset('assets/images/default-avatar.png') }}"
                                         class="img-fluid rounded-circle border shadow-sm" alt="Current Profile">
                                </div>
                                <div class="flex-grow-1">
                                    <input type="file" class="form-control" id="profile_pic" name="profile_pic" accept="image/*">
                                    <div class="form-text">Allowed JPG, PNG or GIF. Max size of 2MB</div>
                                </div>
                            </div>
                            @error('profile_pic') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-end gap-2 mt-5">
                            <a href="{{ route('profile.show') }}" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="ri-save-line me-1"></i> Save Changes
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
