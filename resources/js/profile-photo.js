// Profile Photo Management Functions

class ProfilePhotoManager {
    constructor() {
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    async uploadPhoto(file, userId = null) {
        const formData = new FormData();
        formData.append('avatar', file);
        
        // Update to use the correct web route
        let url = '/api/profile/photo';
        if (userId) {
            url = `/api/users/${userId}/photo`;
        }

        try {
            const response = await axios.post(url, formData, {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': this.csrfToken
                }
            });

            if (response.data.profile_photo_url) {
                return {
                    success: true,
                    profile_photo_url: response.data.profile_photo_url,
                    profile_photo_path: response.data.profile_photo_path,
                    message: response.data.message
                };
            }
        } catch (error) {
            console.error('Error uploading photo:', error);
            return {
                success: false,
                message: error.response?.data?.message || error.message || 'Error uploading profile photo'
            };
        }
    }

    async removePhoto(userId = null) {
        // Update to use the correct web route
        let url = '/api/profile/photo';
        if (userId) {
            url = `/api/users/${userId}/photo`;
        }
        
        try {
            const response = await axios.delete(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': this.csrfToken
                }
            });

            if (response.data.profile_photo_url) {
                return {
                    success: true,
                    profile_photo_url: response.data.profile_photo_url,
                    message: response.data.message
                };
            }
        } catch (error) {
            console.error('Error removing photo:', error);
            return {
                success: false,
                message: error.response?.data?.message || error.message || 'Error removing profile photo'
            };
        }
    }

    async getUserPhoto(userId = null) {
        // Update to use the correct web route
        let url = '/api/profile/photo';
        if (userId) {
            url = `/api/users/${userId}/photo`;
        }
        
        try {
            const response = await axios.get(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            return {
                success: true,
                profile_photo_url: response.data.profile_photo_url,
                profile_photo_path: response.data.profile_photo_path
            };
        } catch (error) {
            console.error('Error getting user photo:', error);
            return {
                success: false,
                message: error.response?.data?.message || error.message || 'Error getting profile photo'
            };
        }
    }
    
    // Utility function to show toast messages
    showToast(message, type = 'info') {
        // Check if global showToast function exists, otherwise create a simple alert
        if (typeof window.showToast === 'function') {
            window.showToast(message, type);
        } else {
            alert(message);
        }
    }
}

// Create a global instance
window.ProfilePhotoManager = new ProfilePhotoManager();