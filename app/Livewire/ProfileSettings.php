<?php

namespace App\Livewire;

use Livewire\Component;

class ProfileSettings extends Component
{
    // Mock user data for demonstration
    public $name = 'John Doe';
    public $email = 'john.doe@example.com';
    public $currentPassword = '';
    public $newPassword = '';
    public $newPasswordConfirmation = '';

    // Message to show on successful save
    public $successMessage = '';

    // Lifecycle hook: runs once after component is instantiated
    public function mount()
    {
        // In a real application, you would load the authenticated user's data here:
        // $user = Auth::user();
        // $this->name = $user->name;
        // $this->email = $user->email;
    }

    // Method to save general profile information
    public function saveProfile()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            // In a real app, you'd add: |unique:users,email,' . Auth::id(),
            'email' => 'required|string|email|max:255',
        ]);

        // Mock update: In a real application, you would update the user record here.

        // Clear any previous password errors when saving the profile
        $this->reset(['currentPassword', 'newPassword', 'newPasswordConfirmation']);
        $this->successMessage = 'Profile information updated successfully!';

        // Clear the success message after 3 seconds
        $this->dispatchBrowserEvent('clear-success-message');

        // Add this script in your Blade view to handle the event
        // <script>
        //     window.addEventListener('clear-success-message', () => {
        //         setTimeout(() => {
        //             Livewire.emit('resetSuccessMessage');
        //         }, 3000);
        //     });
        // </script>
    }

    // Method to save password
    public function savePassword()
    {
        // Add a check for current password (mocked for this demo)
        if ($this->currentPassword !== 'password123') {
             $this->addError('currentPassword', 'The current password you entered is incorrect.');
             return;
        }

        $this->validate([
            'currentPassword' => 'required|string',
            'newPassword' => 'required|string|min:8|same:newPasswordConfirmation',
            'newPasswordConfirmation' => 'required|string|min:8',
        ]);

        // Mock update: In a real application, hash and update the password here.

        $this->reset(['currentPassword', 'newPassword', 'newPasswordConfirmation']);
        $this->successMessage = 'Password updated successfully!';

        // Clear the success message after 3 seconds
        // $this->js('$wire.successMessage = "";')->delay(3000);
    }

    public function render()
    {
        // Use the custom layout we created
        return view('livewire.profile-settings')//->layout('components.layouts.app')
        ;
    }
}