<html>
    <head>Dashboard (secured)</head>
    <body>
        @if(auth()->check())
            <form method="post" action="{{ route('logout.keycloak') }}" id="logoutForm">
                @csrf
                <button type="submit">Logout</button>
            </form>
        @else
            {{-- Display something else if the user is not authenticated --}}
        @endif
        <h1>Hello, {{ $user->name }}</h1>
    </body>
</html>
