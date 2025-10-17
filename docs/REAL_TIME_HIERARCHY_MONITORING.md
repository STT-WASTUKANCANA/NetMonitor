# Real-time Device Hierarchy Monitoring System

## Overview
This document describes the implementation of a true real-time monitoring system that tracks device hierarchy levels (main, sub, and regular devices) with automatic per-second updates. The system provides immediate status updates, accurate device status detection (UP, DOWN, UNKNOWN), and dynamic latency measurements with both automatic and manual refresh options.

## Features Implemented

### 1. API Endpoints
- **GET `/api/devices/hierarchy`** - Retrieves the complete device hierarchy
- **GET `/api/devices/hierarchy/realtime`** - Retrieves real-time hierarchy data with current status and response times
- **POST `/api/devices/bulk-ping`** - Manually trigger immediate refresh of all devices
- **GET `/api/metrics/realtime`** - Get real-time network metrics for dashboard charts

### 2. Real-time Updates
- True per-second updates for all device status and latency data
- WebSocket broadcasting for instant UI updates (via `PerSecondDeviceStatusUpdated` event)
- High-frequency monitoring jobs for rapid data processing
- Automatic detection of UP/DOWN/UNKNOWN device states

### 3. Status Detection
- **UP**: Device responds normally to ping
- **DOWN**: Device fails to respond to ping
- **UNKNOWN**: IP address is missing, invalid, or malformed
- Accurate status indicators with appropriate color coding

### 4. Hierarchy Visualization
- Tree-structured display of device relationships (main → sub → device)
- Real-time status indicators for each hierarchy level
- Response time tracking with visual indicators
- Dynamic latency values in milliseconds updated per second

### 5. Dashboard Components
- Real-time status bars for each hierarchy level (main, sub, device)
- Auto-updating response time charts with per-second refresh
- Device hierarchy tree visualization with live updates
- Summary statistics with live counters
- Manual refresh button ("Scan" icon) for immediate recheck

### 6. Manual Refresh Functionality
- Dedicated "Scan" button on dashboard triggers immediate device recheck
- Visual feedback during manual refresh process
- Bulk refresh capability for all devices simultaneously
- Real-time updates of results with WebSocket integration

## Technical Implementation

### Backend Components
1. **Per-Second Monitoring Job** - `MonitorDevicesPerSecond` job processes all devices every second
2. **Enhanced Ping Service** - Updated `PingService` now handles UNKNOWN status for invalid IPs
3. **New Events** - `PerSecondDeviceStatusUpdated` event for per-second UI updates
4. **Database Updates** - `device_logs` table now supports 'unknown' status (enum: up/down/unknown)
5. **Device Model** - Updated to handle all three status states with proper validation
6. **API Controllers** - Enhanced `DeviceController` with bulk ping and manual refresh endpoints
7. **Console Command** - `PerSecondMonitor` command for continuous monitoring (optional)

### Frontend Components
1. **WebSocket Integration** - Real-time updates via Pusher for per-second data
2. **Enhanced UI Indicators** - Proper visual representation of UP/DOWN/UNKNOWN states
3. **Manual Refresh Button** - "Scan" button triggers immediate bulk device refresh
4. **Loading Indicators** - Visual feedback during manual refresh operations
5. **Notification System** - Success/error messages for manual refresh actions
6. **Optimized Updates** - Efficient DOM updates to handle per-second changes

### Event System
- `PerSecondDeviceStatusUpdated` - Broadcasts immediate device status changes
- `RealTimeHierarchyUpdated` - Updated to handle per-second hierarchy changes
- `DeviceStatusUpdated` - Existing event still used for regular monitoring

## Data Flow

### Automatic Updates (Per-Second)
1. `MonitorDevicesPerSecond` job runs to check all devices
2. `PingService` verifies device status (UP/DOWN/UNKNOWN)
3. `PerSecondDeviceStatusUpdated` event broadcasts changes instantly
4. Frontend receives real-time updates and updates UI immediately
5. Database logs all changes for historical tracking

### Manual Refresh
1. User clicks "Scan" button on dashboard
2. Frontend calls `/api/devices/bulk-ping` endpoint
3. Backend pings all devices immediately
4. `PerSecondDeviceStatusUpdated` events broadcast results
5. Dashboard UI updates in real-time with visual feedback

## Benefits
- **True Real-time Updates**: Per-second device monitoring with instant UI updates
- **Accurate Status Detection**: Three-state system (UP/DOWN/UNKNOWN) with proper IP validation
- **Immediate Visibility**: Network issues detected and displayed within seconds
- **Manual Control**: "Scan" button provides on-demand refresh capability
- **Historical Tracking**: All status changes stored for analysis and reporting
- **Scalable Architecture**: Queue-based system handles high-frequency monitoring

## Usage
The system provides dual operation modes:

### Automatic Mode
- Device monitoring runs continuously at per-second intervals
- Status and latency data update automatically in real-time
- Dashboard reflects changes immediately without manual intervention

### Manual Mode
- Click the "Scan" button (refresh icon) on the dashboard
- Triggers immediate recheck of all devices
- Visual feedback shows refresh progress and completion status
- Results update in real-time as devices are checked

## Configuration
The system can be configured for different environments:
- Production: Queue-based workers handle high-frequency monitoring
- Development: Simpler scheduling may be used based on performance requirements

## Performance Considerations
- High-frequency monitoring may impact system resources with large device counts
- Queue workers should be configured appropriately for expected device loads
- Database performance should be monitored with frequent write operations