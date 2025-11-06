<x-layouts.auth>
    <div class="container py-5">
    <div class="row justify-content-center">
    <div class="col-md-8 col-lg-6">
    <div class="card shadow-lg rounded-4 p-4 p-md-5">
    <h2 class="fw-bolder text-dark mb-4 border-bottom pb-3 text-center">Create Account</h2>

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

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <!-- Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Full Name</label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="form-control form-control-lg">
                        </div>

                        <!-- Email Address -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email Address</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="form-control form-control-lg">
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">Password</label>
                            <input id="password" type="password" name="password" required autocomplete="new-password" class="form-control form-control-lg">
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="form-control form-control-lg">
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg fw-bold">
                                Register
                            </button>
                        </div>

                        <div class="mt-4 text-center">
                            <a class="text-sm text-muted" href="{{ route('login') }}">
                                Already have an account?
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    </x-layouts.auth>