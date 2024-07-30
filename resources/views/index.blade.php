@php use Illuminate\Support\Facades\Auth; @endphp
@if (Auth::user())
    @switch(Auth::user()->role)
        @case('vendor')
            @include('backend.profile.vendor_dashboard')
        @case('admin')
            @include('backend.profile.admin_dashboard')
        @case('user')
            @include('backend.profile.user_dashboard')
        @endswitch
    @else
        @include('auth.login')
    @endif
