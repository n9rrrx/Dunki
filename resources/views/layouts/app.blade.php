<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <title>Dunki | The ultimate way to get outta country</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}">

    {{-- 🛑 VITE ENTRY POINT FOR CSS & JS (CORRECT WAY FOR ECHO/LIVE RELOAD) --}}
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <!-- CSS (Legacy/Libs - Keep for now) -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.6.0/css/select.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/jquery-datatables-checkboxes@1.3.0/css/dataTables.checkboxes.min.css" rel="stylesheet">

    <!-- Layout JS -->
    <script src="{{ asset('assets/js/layout/layout-default.js') }}"></script>
    <script src="{{ asset('assets/js/layout/layout.js') }}"></script>

    <!-- App CSS (Keep as fallbacks if not using custom SCSS) -->
    <link rel="stylesheet" href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}">
    <link href="{{ asset('assets/libs/simplebar/simplebar.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" id="bootstrap-style" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css">
    <link href="{{ asset('assets/css/custom.min.css') }}" id="custom-style" rel="stylesheet" type="text/css">
</head>

<body>
<!-- HEADER -->
@include('components.header')

<!-- SIDEBAR -->
<aside class="app-sidebar">
    <!-- BRAND LOGO -->
    <div class="app-sidebar-logo px-6 justify-content-center align-items-center">
        <a href="{{ route('home') }}">
            <img height="35" class="app-sidebar-logo-default" alt="Logo" src="{{ asset('assets/images/light-logo.png') }}">
            <img height="40" class="app-sidebar-logo-minimize" alt="Logo" src="{{ asset('assets/images/Favicon.png') }}">
        </a>
    </div>

    <!-- MENU -->
    <nav class="app-sidebar-menu nav nav-pills flex-column fs-6" id="sidebarMenu" aria-label="Main navigation">
        <ul class="main-menu" id="all-menu-items" role="menu">

            {{-- 🎓 STUDENT MENU --}}
            @if(auth()->user()->user_type === 'student')
                <li class="slide">
                    <a href="{{ route('student.dashboard') }}" class="side-menu__item">
                        <span class="side_menu_icon"><i class="ri-home-2-line"></i></span>
                        <span class="side-menu__label">Dashboard</span>
                    </a>
                </li>
                <li class="slide">
                    <a href="{{ route('profile.edit') }}" class="side-menu__item">
                        <span class="side_menu_icon"><i class="ri-user-line"></i></span>
                        <span class="side-menu__label">My Profile</span>
                    </a>
                </li>
                <li class="slide">
                    <a href="{{ route('universities.index') }}" class="side-menu__item">
                        <span class="side_menu_icon"><i class="ri-bank-line"></i></span>
                        <span class="side-menu__label">Find Universities</span>
                    </a>
                </li>
                <li class="slide">
                    <a href="{{ route('applications.index') }}" class="side-menu__item">
                        <span class="side_menu_icon"><i class="ri-file-list-3-line"></i></span>
                        <span class="side-menu__label">My Applications</span>
                    </a>
                </li>
                <li class="slide">
                    <a href="{{ route('files.index') }}" class="side-menu__item">
                        <span class="side_menu_icon"><i class="ri-folder-shield-2-line"></i></span>
                        <span class="side-menu__label">Documents</span>
                    </a>
                </li>

                {{-- ✅ STUDENT CHAT BADGE --}}
                <li class="slide">
                    <a href="{{ route('chat.index') }}" class="side-menu__item position-relative">
                        <span class="side_menu_icon"><i class="ri-chat-1-line"></i></span>
                        <span class="side-menu__label">Advisor Chat</span>

                        <span id="sidebar-badge-student"
                              class="position-absolute top-50 end-0 translate-middle-y badge rounded-pill bg-danger {{ ($globalUnreadCount ?? 0) > 0 ? '' : 'd-none' }}"
                              style="margin-right: 15px;">
                                {{ ($globalUnreadCount ?? 0) > 9 ? '9+' : ($globalUnreadCount ?? 0) }}
                            </span>
                    </a>
                </li>
            @endif

            {{-- 🛡️ ADMIN MENU --}}
            @if(auth()->user()->user_type === 'admin')
                <li class="menu-title">System Overview</li>

                <li class="slide">
                    <a href="{{ route('admin.dashboard') }}" class="side-menu__item">
                        <span class="side_menu_icon"><i class="ri-dashboard-line"></i></span>
                        <span class="side-menu__label">Admin Dashboard</span>
                    </a>
                </li>

                <li class="menu-title">Management</li>

                <li class="slide">
                    <a href="{{ route('admin.universities.index') }}" class="side-menu__item">
                        <span class="side_menu_icon"><i class="ri-building-4-line"></i></span>
                        <span class="side-menu__label">Manage Universities</span>
                    </a>
                </li>

                <li class="slide">
                    <a href="{{ route('students.index') }}" class="side-menu__item">
                        <span class="side_menu_icon"><i class="ri-group-line"></i></span>
                        <span class="side-menu__label">Users & Staff</span>
                    </a>
                </li>

                <li class="slide">
                    <a href="#!" class="side-menu__item">
                        <span class="side_menu_icon"><i class="ri-file-settings-line"></i></span>
                        <span class="side-menu__label">All Applications</span>
                    </a>
                </li>
            @endif

            {{-- 🎓 ACADEMIC ADVISOR MENU --}}
            @if(auth()->user()->user_type === 'academic_advisor')
                <li class="menu-title">Work Space</li>

                <li class="slide">
                    <a href="{{ route('academic.dashboard') }}" class="side-menu__item">
                        <span class="side_menu_icon"><i class="ri-dashboard-line"></i></span>
                        <span class="side-menu__label">Advisor Dashboard</span>
                    </a>
                </li>

                <li class="slide">
                    <a href="{{ route('applications.index') }}" class="side-menu__item">
                        <span class="side_menu_icon"><i class="ri-file-list-3-line"></i></span>
                        <span class="side-menu__label">Review Applications</span>
                    </a>
                </li>

                {{-- ✅ ADVISOR CHAT BADGE --}}
                <li class="slide">
                    <a href="{{ route('chat.index') }}" class="side-menu__item position-relative">
                        <span class="side_menu_icon"><i class="ri-chat-smile-2-line"></i></span>
                        <span class="side-menu__label">Messages</span>

                        <span id="sidebar-badge-advisor"
                              class="position-absolute top-50 end-0 translate-middle-y badge rounded-pill bg-danger {{ ($globalUnreadCount ?? 0) > 0 ? '' : 'd-none' }}"
                              style="margin-right: 15px;">
                                {{ ($globalUnreadCount ?? 0) > 9 ? '9+' : ($globalUnreadCount ?? 0) }}
                            </span>
                    </a>
                </li>
            @endif

            {{-- ✈️ VISA CONSULTANT MENU --}}
            @if(auth()->user()->user_type === 'visa_consultant')
                <li class="menu-title">Visa Center</li>

                <li class="slide">
                    <a href="{{ route('consultant.dashboard') }}" class="side-menu__item">
                        <span class="side_menu_icon"><i class="ri-passport-line"></i></span>
                        <span class="side-menu__label">Visa Processing</span>
                    </a>
                </li>

                <li class="slide">
                    <a href="#!" class="side-menu__item">
                        <span class="side_menu_icon"><i class="ri-task-line"></i></span>
                        <span class="side-menu__label">My Tasks</span>
                    </a>
                </li>

                {{-- Added Chat Link for Visa Consultant as well (Optional but good) --}}
                <li class="slide">
                    <a href="{{ route('chat.index') }}" class="side-menu__item">
                        <span class="side_menu_icon"><i class="ri-chat-1-line"></i></span>
                        <span class="side-menu__label">Messages</span>
                    </a>
                </li>
            @endif

            {{-- 🌍 TRAVEL AGENT MENU (ADDED HERE) --}}
            @if(auth()->user()->user_type === 'travel_agent')
                <li class="menu-title">Logistics</li>

                <li class="slide">
                    <a href="{{ route('travel.dashboard') }}" class="side-menu__item">
                        <span class="side_menu_icon"><i class="ri-plane-line"></i></span>
                        <span class="side-menu__label">Travel Dashboard</span>
                    </a>
                </li>

                <li class="slide">
                    <a href="{{ route('applications.index') }}" class="side-menu__item">
                        <span class="side_menu_icon"><i class="ri-file-list-3-line"></i></span>
                        <span class="side-menu__label">Bookings List</span>
                    </a>
                </li>

                {{-- ✅ TRAVEL AGENT CHAT BADGE --}}
                <li class="slide">
                    <a href="{{ route('chat.index') }}" class="side-menu__item position-relative">
                        <span class="side_menu_icon"><i class="ri-chat-smile-2-line"></i></span>
                        <span class="side-menu__label">Messages</span>

                        <span id="sidebar-badge-travel"
                              class="position-absolute top-50 end-0 translate-middle-y badge rounded-pill bg-danger {{ ($globalUnreadCount ?? 0) > 0 ? '' : 'd-none' }}"
                              style="margin-right: 15px;">
                                {{ ($globalUnreadCount ?? 0) > 9 ? '9+' : ($globalUnreadCount ?? 0) }}
                            </span>
                    </a>
                </li>
            @endif

        </ul>
    </nav>
</aside>
<!-- END SIDEBAR -->

<div class="horizontal-overlay"></div>

<!-- MAIN CONTENT -->
<main class="app-wrapper">
    <div class="app-container">

        {{-- ALERTS --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
                <i class="ri-checkbox-circle-line me-2 align-middle fs-5"></i>
                <strong>Success!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
                <i class="ri-error-warning-line me-2 align-middle fs-5"></i>
                <strong>Hold on!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
                <i class="ri-close-circle-line me-2 align-middle fs-5"></i>
                <strong>Please check the form:</strong>
                <ul class="mb-0 mt-1 ps-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>
</main>

<!-- FOOTER -->
@include('components.footer')

<!-- JAVASCRIPT -->
<script src="{{ asset('assets/js/sidebar.js') }}"></script>
<script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/scroll-top.init.js') }}"></script>
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<!-- DataTables -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/select/1.6.0/js/dataTables.select.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-datatables-checkboxes@1.3.0/js/dataTables.checkboxes.min.js"></script>

<!-- App JS (REMOVED REDUNDANT/FAULTY ASSET IMPORT) -->
{{-- This line should be removed as @vite handles it --}}
{{-- <script type="module" src="{{ asset('assets/js/app.js') }}"></script> --}}

@stack('scripts')

{{-- ✅ GLOBAL NOTIFICATION LISTENER --}}
@auth
    <script type="module">
        document.addEventListener('DOMContentLoaded', function() {
            const currentUserId = {{ Auth::id() }};

            // Select the badge based on role (Added Travel Agent support)
            const badgeStudent = document.getElementById('sidebar-badge-student');
            const badgeAdvisor = document.getElementById('sidebar-badge-advisor');
            const badgeTravel = document.getElementById('sidebar-badge-travel');
            const onChatPage = window.location.pathname.includes('/chat');

            let activeBadge = null;
            @if(Auth::user()->user_type === 'student')
                activeBadge = badgeStudent;
            @elseif(Auth::user()->user_type === 'academic_advisor')
                activeBadge = badgeAdvisor;
            @elseif(Auth::user()->user_type === 'travel_agent')
                activeBadge = badgeTravel;
            // Note: Visa consultant badge support should be added if needed
            @endif


            // 1. Initial State: If on chat page, hide badge immediately
            if (onChatPage && activeBadge) {
                // This is handled by the chat view itself, but good safety check
                activeBadge.innerText = '0';
                activeBadge.classList.add('d-none');
            }

            if (activeBadge) {
                console.log("✅ Sidebar Badge Listener Active for User:", currentUserId);

                setTimeout(() => {
                    // Check window.Echo availability after a slight delay
                    if(window.Echo) {
                        window.Echo.private(`chat.${currentUserId}`)
                            .listen('MessageSent', (e) => {
                                console.log("🔔 Event Received:", e);

                                // Only notify if NOT on chat page
                                if (!onChatPage) {

                                    // A. Show Toast (SweetAlert)
                                    if (typeof Swal !== 'undefined') {
                                        Swal.fire({
                                            toast: true,
                                            position: 'top-end',
                                            icon: 'info',
                                            title: 'New Message',
                                            text: e.message,
                                            showConfirmButton: false,
                                            timer: 3000,
                                            timerProgressBar: true
                                        });
                                    }

                                    // B. Update Badge Logic
                                    let currentText = activeBadge.innerText.trim();
                                    let currentCount = 0;

                                    // Handle '9+' or empty string cases
                                    if (currentText === '9+') {
                                        currentCount = 10;
                                    } else if (currentText !== '') {
                                        currentCount = parseInt(currentText);
                                        if (isNaN(currentCount)) currentCount = 0;
                                    }

                                    let newCount = currentCount + 1;
                                    console.log(`🔢 Updating Badge: ${currentCount} -> ${newCount}`);

                                    // Update Text
                                    activeBadge.innerText = newCount > 9 ? '9+' : newCount;

                                    // Force Visibility
                                    activeBadge.classList.remove('d-none');
                                    activeBadge.style.display = 'inline-block';

                                    // Add a little animation pop
                                    activeBadge.classList.add('animate__animated', 'animate__pulse');
                                }
                            });
                    }
                }, 1000);
            }
        });
    </script>
@endauth

</body>
</html>
