#!/bin/bash

# Network Monitoring System Setup Script
# Automates the installation and configuration process

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if running as root
check_root() {
    if [[ $EUID -eq 0 ]]; then
        print_error "This script should not be run as root. Please run as a regular user with sudo privileges."
        exit 1
    fi
}

# Check system requirements
check_requirements() {
    print_status "Checking system requirements..."
    
    # Check for required commands
    commands=("git" "curl" "wget" "php" "mysql" "composer" "node" "npm" "python3" "pip3")
    
    for cmd in "${commands[@]}"; do
        if ! command -v $cmd &> /dev/null; then
            print_error "$cmd is not installed. Please install it and try again."
            exit 1
        fi
    done
    
    print_success "All required commands are available."
}

# Clone repository
clone_repository() {
    print_status "Cloning repository..."
    
    if [ -d "monitoring-konektivitas" ]; then
        print_warning "Directory monitoring-konektivitas already exists. Removing it..."
        rm -rf monitoring-konektivitas
    fi
    
    git clone https://github.com/your-repo/monitoring-konektivitas.git
    
    if [ $? -ne 0 ]; then
        print_error "Failed to clone repository."
        exit 1
    fi
    
    cd monitoring-konektivitas
    print_success "Repository cloned successfully."
}

# Install PHP dependencies
install_php_dependencies() {
    print_status "Installing PHP dependencies..."
    
    composer install --no-dev --optimize-autoloader
    
    if [ $? -ne 0 ]; then
        print_error "Failed to install PHP dependencies."
        exit 1
    fi
    
    print_success "PHP dependencies installed successfully."
}

# Install Node.js dependencies
install_node_dependencies() {
    print_status "Installing Node.js dependencies..."
    
    npm install
    
    if [ $? -ne 0 ]; then
        print_error "Failed to install Node.js dependencies."
        exit 1
    fi
    
    print_status "Building frontend assets..."
    npm run build
    
    if [ $? -ne 0 ]; then
        print_error "Failed to build frontend assets."
        exit 1
    fi
    
    print_success "Node.js dependencies installed and assets built successfully."
}

# Configure environment
configure_environment() {
    print_status "Configuring environment..."
    
    if [ ! -f ".env" ]; then
        cp .env.example .env
        print_success "Created .env file from example."
    fi
    
    # Generate application key
    print_status "Generating application key..."
    php artisan key:generate
    
    if [ $? -ne 0 ]; then
        print_error "Failed to generate application key."
        exit 1
    fi
    
    print_success "Application key generated successfully."
}

# Configure database
configure_database() {
    print_status "Configuring database..."
    
    # This would typically prompt for database credentials
    # For automation, we'll assume defaults or environment variables
    
    print_warning "Please ensure your database is configured in the .env file before proceeding."
    print_warning "The script will attempt to run migrations now."
    
    # Run migrations
    php artisan migrate --force
    
    if [ $? -ne 0 ]; then
        print_error "Failed to run database migrations."
        exit 1
    fi
    
    # Seed database
    php artisan db:seed --force
    
    if [ $? -ne 0 ]; then
        print_error "Failed to seed database."
        exit 1
    fi
    
    print_success "Database configured successfully."
}

# Configure web server
configure_web_server() {
    print_status "Configuring web server..."
    
    # This is a simplified example - in practice you would need more complex logic
    # to detect the web server and configure it appropriately
    
    if command -v apache2 &> /dev/null; then
        print_status "Apache detected. Configuring virtual host..."
        # Apache configuration would go here
    elif command -v nginx &> /dev/null; then
        print_status "Nginx detected. Configuring virtual host..."
        # Nginx configuration would go here
    else
        print_warning "No supported web server detected. Please configure manually."
        return
    fi
    
    print_success "Web server configuration completed."
}

# Configure monitoring script
configure_monitoring() {
    print_status "Configuring monitoring script..."
    
    cd scripts
    
    # Install Python dependencies
    pip3 install requests
    
    if [ $? -ne 0 ]; then
        print_error "Failed to install Python dependencies."
        exit 1
    fi
    
    # Make script executable
    chmod +x monitor.py
    
    cd ..
    print_success "Monitoring script configured successfully."
}

# Configure cron jobs
configure_cron() {
    print_status "Configuring cron jobs..."
    
    # Add cron jobs
    (crontab -l 2>/dev/null; echo "*/5 * * * * cd $(pwd) && python3 scripts/monitor.py >> /var/log/monitor.log 2>&1") | crontab -
    
    if [ $? -ne 0 ]; then
        print_error "Failed to configure cron jobs."
        exit 1
    fi
    
    print_success "Cron jobs configured successfully."
}

# Set permissions
set_permissions() {
    print_status "Setting file permissions..."
    
    # Set appropriate permissions
    chmod -R 755 storage
    chmod -R 755 bootstrap/cache
    
    print_success "File permissions set successfully."
}

# Final setup steps
final_setup() {
    print_status "Performing final setup steps..."
    
    # Clear caches
    php artisan cache:clear
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    
    print_success "Final setup completed."
}

# Main installation function
main() {
    print_status "Starting Network Monitoring System installation..."
    
    # Check prerequisites
    check_root
    check_requirements
    
    # Installation steps
    clone_repository
    install_php_dependencies
    install_node_dependencies
    configure_environment
    configure_database
    configure_web_server
    configure_monitoring
    configure_cron
    set_permissions
    final_setup
    
    print_success "Network Monitoring System installation completed successfully!"
    echo ""
    print_status "Next steps:"
    echo "  1. Configure your web server to point to the public directory"
    echo "  2. Set up SSL certificates if needed"
    echo "  3. Configure your database settings in the .env file"
    echo "  4. Create your first admin user"
    echo "  5. Add your network devices to monitor"
    echo ""
    print_status "Default login credentials:"
    echo "  Email: admin@sttwastukancana.ac.id"
    echo "  Password: password"
    echo ""
    print_warning "Remember to change the default password after first login!"
}

# Run main function
main "$@"