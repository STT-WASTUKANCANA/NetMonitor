# 👤 User Guide

Welcome to the Network Monitoring System User Guide. This guide will help you navigate and use all features of the system effectively.

## 🎯 Getting Started

### Login
1. Navigate to the application URL (e.g., http://localhost:8000)
2. Click "Login" in the top right corner
3. Enter your credentials:
   - **Admin:** admin@sttwastukancana.ac.id / password
   - **Petugas:** petugas@sttwastukancana.ac.id / password
4. Click "Sign in"

### Dashboard Overview
After logging in, you'll be directed to the dashboard which displays:
- **Total Devices:** Number of monitored devices
- **Active Devices:** Devices currently online
- **Inactive Devices:** Devices currently offline
- **Active Alerts:** Current unresolved alerts
- **Device Hierarchy:** Visual representation of network structure
- **Recent Alerts:** Latest system notifications

## 🖥️ Navigation

The main navigation menu includes:
- **Dashboard:** System overview and statistics
- **Devices:** Device management section
- **Alerts:** Notification management
- **Reports:** Performance reports and analytics
- **Settings:** (Admin only) System configuration
- **Profile:** User account settings

## 📱 Devices Management

### Viewing Devices
Click "Devices" in the navigation menu to view all network devices organized in a hierarchical structure:
- **Utama Level:** Main network infrastructure (routers, main switches)
- **Sub Level:** Distribution devices (distribution switches)
- **Device Level:** End devices (access points, servers, printers)

Each device card shows:
- Device name and IP address
- Current status (Up/Down)
- Location
- Child devices (if any)

### Adding New Devices
1. Click "Devices" in navigation
2. Click "Add Device" button
3. Fill in device details:
   - **Name:** Descriptive device name
   - **IP Address:** Valid IPv4 address
   - **Type:** Router, Switch, Access Point, Server, or Other
   - **Hierarchy Level:** Utama, Sub, or Device
   - **Parent Device:** Select parent if applicable
   - **Location:** Physical location of device
   - **Description:** Additional notes
4. Toggle "Active" switch to enable monitoring
5. Click "Save Device"

### Editing Devices
1. Navigate to Devices section
2. Find the device you want to edit
3. Click the pencil icon next to the device
4. Make necessary changes
5. Click "Update Device"

### Deleting Devices
1. Navigate to Devices section
2. Find the device you want to delete
3. Click the trash can icon next to the device
4. Confirm deletion in the popup dialog

## ⚠️ Alert Management

### Viewing Alerts
Click "Alerts" in the navigation menu to view all system notifications:
- **Active Alerts:** Currently unresolved issues
- **Resolved Alerts:** Previously handled notifications

Each alert shows:
- Affected device name
- Alert message
- Timestamp
- Current status

### Resolving Alerts
1. Navigate to Alerts section
2. Find the alert you want to resolve
3. Click "Resolve" button
4. The alert status will change to "Resolved"

## 📊 Reports and Analytics

### Performance Reports
1. Click "Reports" in navigation menu
2. Select date range and devices to include
3. Choose report type:
   - **Summary Report:** High-level overview
   - **Detailed Report:** In-depth analysis
   - **Custom Report:** Specific metrics
4. Click "Generate Report"

### PDF Export
1. After generating a report, click "Export to PDF"
2. The report will download automatically
3. Open with any PDF reader

### Filtering Options
Reports can be filtered by:
- **Date Range:** Daily, Weekly, Monthly, Yearly
- **Device Type:** Routers, Switches, Access Points, etc.
- **Status:** Up, Down, or Both
- **Location:** Specific building or floor

## 🌙 Dark Mode

The system supports both light and dark themes:
1. Click the moon/sun icon in the top right corner
2. Toggle between light and dark modes
3. Your preference is saved automatically

## 👤 Profile Settings

### Updating Profile Information
1. Click your username in the top right corner
2. Select "Profile" from dropdown
3. Update your:
   - Name
   - Email address
   - Profile photo (optional)
4. Click "Save Changes"

### Changing Password
1. From Profile page, click "Change Password" tab
2. Enter current password
3. Enter new password twice for confirmation
4. Click "Update Password"

## 🔧 Admin Functions

Administrators have additional capabilities:

### User Management
1. Click "Settings" in navigation menu
2. Select "Users" tab
3. View, add, edit, or delete user accounts
4. Assign roles (Admin or Petugas)

### System Configuration
1. Click "Settings" in navigation menu
2. Configure:
   - Monitoring intervals
   - Alert thresholds
   - Email notifications
   - System branding

### Role Management
1. Click "Settings" in navigation menu
2. Select "Roles" tab
3. Create, edit, or delete roles
4. Assign permissions to roles

## 🐍 Python Monitoring Script

The system includes a Python monitoring script for automated device checking:

### Setup
1. Install Python 3.6 or higher
2. Install required packages:
   ```bash
   pip install requests
   ```

### Configuration
Set environment variables:
```bash
export API_BASE_URL="http://your-domain.com"
export API_TOKEN="your-api-token"
```

### Running the Script
```bash
cd scripts
python3 monitor.py
```

### Scheduling
Add to crontab for automatic execution every 5 minutes:
```bash
*/5 * * * * cd /path/to/project/scripts && python3 monitor.py >> /var/log/monitor.log 2>&1
```

## 🆘 Troubleshooting

### Common Issues

**Cannot Login:**
- Verify username and password
- Check if account is active
- Reset password if needed

**Device Shows Offline:**
- Verify IP address is correct
- Check physical device status
- Ensure device allows ping requests
- Test connectivity manually

**Alerts Not Clearing:**
- Verify device is actually back online
- Manually resolve persistent alerts
- Check monitoring script logs

**Reports Not Generating:**
- Verify date range selection
- Check device selection
- Ensure sufficient data exists

### Contact Support
If you continue to experience issues:
1. Document the problem with screenshots
2. Note the time and steps taken
3. Contact system administrator

## 📱 Mobile Usage

The system is fully responsive and works on mobile devices:
- All navigation adapts to smaller screens
- Touch-friendly controls
- Optimized layouts for vertical viewing
- Same functionality as desktop version

## 🔒 Security Best Practices

### Password Security
- Use strong passwords with mixed characters
- Change passwords regularly
- Never share credentials
- Enable two-factor authentication if available

### Session Management
- Always log out when finished
- Avoid using public computers
- Clear browser cache periodically
- Report suspicious activity

## 🔄 Regular Maintenance

For system administrators:

### Daily Checks
- Review active alerts
- Monitor system performance
- Check disk space and backups

### Weekly Tasks
- Update device inventory
- Review user access
- Clean up resolved alerts

### Monthly Reviews
- Analyze performance reports
- Audit user permissions
- Review system logs
- Update documentation