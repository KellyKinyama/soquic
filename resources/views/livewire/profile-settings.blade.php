<div class="row justify-content-center">
    <!-- Page Title -->
    <div class="col-12">
        <h1 class="text-primary mb-4">Profile Settings</h1>
    </div>

    <!-- Alert for Success Message -->
    @if ($successMessage)
        <div class="col-lg-8">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ $successMessage }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    <!-- Profile Information Card -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white border-bottom-0 rounded-top-3">
                <h5 class="mb-0 text-dark">General Information</h5>
                <p class="text-muted small mb-0">Update your name and email address.</p>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="saveProfile">
                    <!-- Name Input -->
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Name</label>
                        <input wire:model.defer="name" type="text" class="form-control @error('name') is-invalid @enderror rounded-2" id="name" placeholder="Enter your full name">
                        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- Email Input -->
                    <div class="mb-4">
                        <label for="email" class="form-label fw-bold">Email address</label>
                        <input wire:model.defer="email" type="email" class="form-control @error('email') is-invalid @enderror rounded-2" id="email" placeholder="Enter your email">
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary rounded-2 shadow-sm" wire:loading.attr="disabled">
                            <span wire:loading wire:target="saveProfile" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            Save Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Password Update Card -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-header bg-white border-bottom-0 rounded-top-3">
                <h5 class="mb-0 text-dark">Update Password</h5>
                <p class="text-muted small mb-0">Ensure your password is at least 8 characters long.</p>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="savePassword">
                    <!-- Current Password Input -->
                    <div class="mb-3">
                        <label for="currentPassword" class="form-label fw-bold">Current Password</label>
                        <input wire:model.defer="currentPassword" type="password" class="form-control @error('currentPassword') is-invalid @enderror rounded-2" id="currentPassword" placeholder="Enter current password">
                        @error('currentPassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div id="passwordHelpBlock" class="form-text">
                            (Hint: use 'password123' to mock success in this demo.)
                        </div>
                    </div>

                    <!-- New Password Input -->
                    <div class="mb-3">
                        <label for="newPassword" class="form-label fw-bold">New Password</label>
                        <input wire:model.defer="newPassword" type="password" class="form-control @error('newPassword') is-invalid @enderror rounded-2" id="newPassword" placeholder="Enter new password (min 8 characters)">
                        @error('newPassword') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <!-- New Password Confirmation Input -->
                    <div class="mb-4">
                        <label for="newPasswordConfirmation" class="form-label fw-bold">Confirm New Password</label>
                        <input wire:model.defer="newPasswordConfirmation" type="password" class="form-control @error('newPasswordConfirmation') is-invalid @enderror rounded-2" id="newPasswordConfirmation" placeholder="Confirm new password">
                        @error('newPasswordConfirmation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-warning rounded-2 shadow-sm" wire:loading.attr="disabled">
                            <span wire:loading wire:target="savePassword" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>