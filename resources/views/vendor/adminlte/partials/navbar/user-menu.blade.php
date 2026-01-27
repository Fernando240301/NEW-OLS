@php
    $user = Auth::user();
@endphp

<li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#">
        {{ $user->name }}
    </a>

    <div class="dropdown-menu dropdown-menu-right">
        <a href="{{ url('/activity-log') }}" class="dropdown-item">
            <i class="fas fa-history mr-2"></i> Log Activity
        </a>

        <div class="dropdown-divider"></div>

        <a href="{{ route('logout') }}" class="dropdown-item"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt mr-2 text-danger"></i> Log Out
        </a>

        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
            @csrf
        </form>
    </div>
</li>
