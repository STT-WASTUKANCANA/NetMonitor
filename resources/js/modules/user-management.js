// User Management Module
// Handles all user management operations for the frontend

class UserManagement {
    constructor() {
        // Extract CSRF token from meta tag
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    }

    // Get CSRF token
    getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
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
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
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

    // Show confirmation dialog
    confirm(message) {
        return confirm(message);
    }

    // Bind delete button event listeners
    bindDeleteButtons() {
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

    // Bind delete button event listeners with modal
    bindDeleteButtonsWithModal() {
        const deleteModal = document.getElementById('deleteModal');
        const userNameToDelete = document.getElementById('userNameToDelete');
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        const cancelDeleteBtn = document.getElementById('cancelDeleteBtn');
        let currentUserId = null;

        if (!deleteModal) return;

        // Handle delete button clicks
        document.querySelectorAll('.delete-user-btn').forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-user-id');
                const userName = this.getAttribute('data-user-name');
                
                currentUserId = userId;
                userNameToDelete.textContent = userName;
                document.getElementById('deleteModalTitle').innerHTML = 'Confirm Delete User';
                
                // Show modal
                deleteModal.classList.remove('hidden');
            });
        });

        // Confirm delete
        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', function() {
                if (currentUserId) {
                    const form = document.getElementById('delete-user-form-' + currentUserId);
                    if (form) {
                        form.submit();
                    }
                }
            });
        }

        // Cancel delete
        if (cancelDeleteBtn) {
            cancelDeleteBtn.addEventListener('click', function() {
                deleteModal.classList.add('hidden');
                currentUserId = null;
            });
        }

        // Close modal when clicking outside
        deleteModal.addEventListener('click', function(e) {
            if (e.target === deleteModal) {
                deleteModal.classList.add('hidden');
                currentUserId = null;
            }
        });
    }

    // Bind form validation
    bindFormValidation() {
        // Handle form submissions with validation
        document.querySelectorAll('form.validate-password').forEach(form => {
            form.addEventListener('submit', function(e) {
                const password = form.querySelector('#password');
                const passwordConfirmation = form.querySelector('#password_confirmation');
                
                if (password && password.value && passwordConfirmation) {
                    if (password.value !== passwordConfirmation.value) {
                        e.preventDefault();
                        this.showNotification('Passwords do not match', 'error');
                        return false;
                    }
                    
                    if (password.value.length < 8) {
                        e.preventDefault();
                        this.showNotification('Password must be at least 8 characters long', 'error');
                        return false;
                    }
                }
            });
        });
    }

    // Initialize user management functionality
    init() {
        // Bind event listeners when DOM is loaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.bindDeleteButtonsWithModal();
                this.bindFormValidation();
            });
        } else {
            this.bindDeleteButtonsWithModal();
            this.bindFormValidation();
        }
    }
}

// Export the class for use in other modules
window.UserManagement = UserManagement;

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.userManagement = new UserManagement();
});

// Initialize user management functionality
const userManagement = new UserManagement();
userManagement.init();

export default UserManagement;