// User Management Module
// Handles all user management operations via AJAX/API

class UserManagement {
    constructor() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        this.baseUrl = '/api/users';
    }

    // Get all users with optional filters
    async getUsers(params = {}) {
        try {
            const urlParams = new URLSearchParams(params);
            const response = await fetch(`/api/users?${urlParams}`, {
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('Error fetching users:', error);
            throw error;
        }
    }

    // Get a specific user
    async getUser(userId) {
        try {
            const response = await fetch(`/api/users/${userId}`, {
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                credentials: 'same-origin'
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        } catch (error) {
            console.error('Error fetching user:', error);
            throw error;
        }
    }

    // Create a new user
    async createUser(userData) {
        try {
            const formData = new FormData();
            
            // Append all user data to form data
            for (const [key, value] of Object.entries(userData)) {
                formData.append(key, value);
            }

            const response = await fetch('/api/users', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                },
                body: formData,
                credentials: 'same-origin'
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Failed to create user');
            }

            return result;
        } catch (error) {
            console.error('Error creating user:', error);
            throw error;
        }
    }

    // Update an existing user
    async updateUser(userId, userData) {
        try {
            const formData = new FormData();
            
            // Append all user data to form data
            for (const [key, value] of Object.entries(userData)) {
                formData.append(key, value);
            }
            
            // Set the method override for PUT requests
            formData.append('_method', 'PUT');

            const response = await fetch(`/api/users/${userId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                },
                body: formData,
                credentials: 'same-origin'
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Failed to update user');
            }

            return result;
        } catch (error) {
            console.error('Error updating user:', error);
            throw error;
        }
    }

    // Delete a user
    async deleteUser(userId) {
        try {
            const response = await fetch(`/api/users/${userId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                credentials: 'same-origin'
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Failed to delete user');
            }

            return result;
        } catch (error) {
            console.error('Error deleting user:', error);
            throw error;
        }
    }

    // Update user profile photo
    async updateProfilePhoto(userId, photoFile) {
        try {
            const formData = new FormData();
            formData.append('profile_photo', photoFile);
            formData.append('_method', 'PUT');

            const response = await fetch(`/api/users/${userId}/photo`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                },
                body: formData,
                credentials: 'same-origin'
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Failed to update profile photo');
            }

            return result;
        } catch (error) {
            console.error('Error updating profile photo:', error);
            throw error;
        }
    }

    // Remove user profile photo
    async removeProfilePhoto(userId) {
        try {
            const response = await fetch(`/api/users/${userId}/photo`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': this.csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                credentials: 'same-origin'
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Failed to remove profile photo');
            }

            return result;
        } catch (error) {
            console.error('Error removing profile photo:', error);
            throw error;
        }
    }

    // Show notification to user
    showNotification(message, type = 'info', duration = 3000) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform transition-all duration-300 ${
            type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' :
            type === 'error' ? 'bg-red-100 text-red-800 border border-red-200' :
            type === 'warning' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' :
            'bg-blue-100 text-blue-800 border border-blue-200'
        }`;
        notification.innerHTML = `
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    ${type === 'success' ? 
                        '<svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' :
                    type === 'error' ? 
                        '<svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>' :
                    type === 'warning' ? 
                        '<svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>' :
                        '<svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
                    }
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium">${message}</p>
                </div>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after specified duration
        setTimeout(() => {
            if (notification.parentNode) {
                notification.style.opacity = '0';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }
        }, duration);
    }

    // Format date for display
    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
    }

    // Validate email format
    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Validate password strength
    isValidPassword(password) {
        // At least 8 characters
        return password && password.length >= 8;
    }
}

// Export the class for use in other modules
window.UserManagement = UserManagement;

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.userManagement = new UserManagement();
    
    // Bind event listeners for delete buttons if they exist
    bindDeleteEventListeners();
});

// Function to bind delete event listeners
function bindDeleteEventListeners() {
    // Handle delete button clicks in index view
    document.querySelectorAll('.delete-user-btn').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            const userName = this.getAttribute('data-user-name');
            
            // Show confirmation dialog
            if (confirm(`Are you sure you want to delete ${userName}? This action cannot be undone.`)) {
                // Submit the delete form
                const form = document.getElementById(`delete-user-form-${userId}`);
                if (form) {
                    form.submit();
                }
            }
        });
    });
}