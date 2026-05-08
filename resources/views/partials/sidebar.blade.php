<div class="sidebar">
    <h4>Menu</h4>

    <a href="{{ route('main') }}">🏠 Dashboard</a>

    <a href="#">📄 Data User</a>
    <a href="#">⚙️ Setting</a>

    <hr>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
</div>
