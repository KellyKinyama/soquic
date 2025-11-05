<x-layouts.app>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card shadow-lg rounded-4 p-4 p-md-5">
                    <h2 class="fw-bolder text-dark mb-4 border-bottom pb-3 text-center">Sign In</h2>

                    <!-- Display Session Status (e.g., password reset success) -->
                    @if (session('status'))
                        <div class="alert alert-success mb-3" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <!-- Display Validation Errors -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <!-- Email Address -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email Address</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                autofocus autocomplete="username" class="form-control form-control-lg">
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <input id="password" type="password" name="password" required
                                autocomplete="current-password" class="form-control form-control-lg">
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                                <label class="form-check-label" for="remember_me">
                                    Remember me
                                </label>
                            </div>

                            @if (Route::has('password.request'))
                                <a class="text-sm text-muted" href="{{ route('password.request') }}">
                                    Forgot your password?
                                </a>
                            @endif
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold">
                                Log In
                            </button>
                        </div>

                        <div class="mt-4 text-center">
                            <a class="text-sm text-muted" href="{{ route('register') }}">
                                Don't have an account? Register
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


</x-layouts.app>
