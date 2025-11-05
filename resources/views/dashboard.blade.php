<x-layouts.app>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                <!-- Welcome Banner -->
                <div class="p-5 mb-4 bg-primary text-white rounded-4 shadow-sm">
                    <h1 class="display-5 fw-bold">Welcome, {{ Auth::user()->name }}!</h1>
                    <p class="lead">This is your personal dashboard. It's the central place to manage your activity and
                        account settings.</p>
                </div>

                <!-- Account Status & Details -->
                <div class="row">
                    <!-- User Details Card -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 shadow-sm rounded-4 border-0">
                            <div class="card-body p-4">
                                <h5 class="card-title fw-bold text-primary mb-3">Account Details</h5>
                                <p class="card-text">
                                    <strong class="d-block mb-1">Email:</strong>
                                    <span class="text-muted">{{ Auth::user()->email }}</span>
                                </p>
                                <p class="card-text">
                                    <strong class="d-block mb-1">Registered Since:</strong>
                                    <span class="text-muted">{{ Auth::user()->created_at->format('M d, Y') }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Card: Manage Account -->
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 shadow-sm rounded-4 border-0 bg-light">
                            <div class="card-body p-4 d-flex flex-column justify-content-between">
                                <div>
                                    <h5 class="card-title fw-bold text-dark mb-3">Quick Actions</h5>
                                    <p class="card-text text-muted">Manage your profile information, password, and
                                        two-factor authentication settings.</p>
                                </div>
                                <a href="#" class="btn btn-outline-primary mt-3 w-100 fw-semibold">
                                    Go to Profile Settings
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Placeholder for future application activity -->
                <div class="card shadow-sm rounded-4 mt-3 border-0">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold text-secondary mb-3">Recent Activity</h5>
                        <p class="text-muted mb-0">No recent activity to display yet. As you use the application,
                            updates and notifications will appear here.</p>
                    </div>
                </div>

            </div>
        </div>
    </div>


</x-layouts.app>
