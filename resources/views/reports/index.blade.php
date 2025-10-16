@extends('layouts.app')

@section('title', 'Performance Reports')

@section('header')
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">Performance Reports</h1>
                    <p class="mt-1 text-sm text-gray-500">Generate and view network performance reports</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <button id="generate-pdf-btn" class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg shadow-sm transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 12l2 2 4-4"></path>
                        </svg>
                        Export PDF
                    </button>
                </div>
            </div>
        </div>
    </header>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Report Filters -->
    <div class="bg-white rounded-xl shadow-card border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Report Filters</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label for="report-period" class="block text-sm font-medium text-gray-700 mb-1">Report Period</label>
                <select id="report-period" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="24h">Last 24 Hours</option>
                    <option value="7d" selected>Last 7 Days</option>
                    <option value="30d">Last 30 Days</option>
                    <option value="90d">Last 90 Days</option>
                    <option value="1y">Last Year</option>
                    <option value="custom">Custom Range</option>
                </select>
            </div>
            
            <div id="custom-date-range" class="hidden">
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" id="start_date" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            
            <div id="custom-date-range-end" class="hidden">
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" id="end_date" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="device-select" class="block text-sm font-medium text-gray-700 mb-1">Select Device</label>
                <select id="device-select" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All Devices</option>
                    @foreach(\App\Models\Device::all() as $device)
                        <option value="{{ $device->id }}">{{ $device->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex items-end">
                <button id="generate-report-btn" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-sm transition-colors">
                    Generate Report
                </button>
            </div>
        </div>
    </div>

    <!-- Report Summary -->
    <div class="bg-white rounded-xl shadow-card border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Report Summary</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                <div class="text-sm text-blue-800">Total Devices</div>
                <div class="text-2xl font-bold text-blue-900 mt-1" id="total-devices">0</div>
            </div>
            <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                <div class="text-sm text-green-800">Uptime %</div>
                <div class="text-2xl font-bold text-green-900 mt-1" id="uptime-percent">0%</div>
            </div>
            <div class="bg-red-50 p-4 rounded-lg border border-red-100">
                <div class="text-sm text-red-800">Total Alerts</div>
                <div class="text-2xl font-bold text-red-900 mt-1" id="total-alerts">0</div>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg border border-purple-100">
                <div class="text-sm text-purple-800">Avg Response</div>
                <div class="text-2xl font-bold text-purple-900 mt-1" id="avg-response">0 ms</div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-card p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Uptime Trend</h3>
            <div class="h-80">
                <canvas id="uptimeChart"></canvas>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-card p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Response Time Trend</h3>
            <div class="h-80">
                <canvas id="responseTimeChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Detailed Data Table -->
    <div class="bg-white rounded-xl shadow-card border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Detailed Data</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="report-data-table">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Device</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uptime %</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg Response</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Max Response</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min Response</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alerts</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Data will be populated by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    class ReportManager {
        constructor() {
            this.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            this.uptimeChart = null;
            this.responseTimeChart = null;
            this.init();
        }
        
        init() {
            this.initCharts();
            this.initEventListeners();
            this.loadDefaultReport();
        }
        
        initCharts() {
            // Initialize uptime chart
            const uptimeCtx = document.getElementById('uptimeChart').getContext('2d');
            this.uptimeChart = new Chart(uptimeCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Uptime %',
                        data: [],
                        borderColor: 'rgb(79, 70, 229)',
                        backgroundColor: 'rgba(79, 70, 229, 0.1)',
                        tension: 0.4,
                        fill: true,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            title: {
                                display: true,
                                text: 'Percentage'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Time'
                            }
                        }
                    }
                }
            });
            
            // Initialize response time chart
            const responseCtx = document.getElementById('responseTimeChart').getContext('2d');
            this.responseTimeChart = new Chart(responseCtx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Avg Response Time (ms)',
                        data: [],
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.4,
                        fill: true,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Response Time (ms)'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Time'
                            }
                        }
                    }
                }
            });
        }
        
        initEventListeners() {
            // Report period change
            document.getElementById('report-period').addEventListener('change', (e) => {
                const period = e.target.value;
                if (period === 'custom') {
                    document.getElementById('custom-date-range').classList.remove('hidden');
                    document.getElementById('custom-date-range-end').classList.remove('hidden');
                } else {
                    document.getElementById('custom-date-range').classList.add('hidden');
                    document.getElementById('custom-date-range-end').classList.add('hidden');
                }
            });
            
            // Generate report button
            document.getElementById('generate-report-btn').addEventListener('click', () => {
                this.generateReport();
            });
            
            // Export PDF button
            document.getElementById('generate-pdf-btn').addEventListener('click', () => {
                this.generatePDF();
            });
        }
        
        loadDefaultReport() {
            // Load a default report (last 7 days)
            this.generateReport('7d');
        }
        
        async generateReport(period = null) {
            try {
                // Get parameters from UI
                const reportPeriod = period || document.getElementById('report-period').value;
                const deviceId = document.getElementById('device-select').value;
                
                let params = new URLSearchParams({
                    report_type: 'detailed',
                    period: reportPeriod
                });
                
                if (deviceId) {
                    params.append('device_id', deviceId);
                }
                
                // For custom date range
                if (reportPeriod === 'custom') {
                    const startDate = document.getElementById('start_date').value;
                    const endDate = document.getElementById('end_date').value;
                    
                    if (!startDate || !endDate) {
                        alert('Please select both start and end dates for custom range');
                        return;
                    }
                    
                    params.set('start_date', startDate);
                    params.set('end_date', endDate);
                }
                
                const response = await fetch(`/api/reports/generate?${params}`, {
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                
                if (response.ok) {
                    const result = await response.json();
                    this.displayReport(result.report_data);
                } else {
                    const error = await response.json();
                    console.error('Error generating report:', error);
                    alert('Error generating report: ' + (error.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error generating report:', error);
                alert('Error connecting to server: ' + error.message);
            }
        }
        
        displayReport(data) {
            // Update summary cards
            if (data.summary) {
                document.getElementById('total-devices').textContent = data.summary.total_checks;
                document.getElementById('uptime-percent').textContent = data.summary.success_rate + '%';
                document.getElementById('total-alerts').textContent = data.detailed ? 
                    (data.detailed.top_issue_devices ? data.detailed.top_issue_devices.length : 0) : '0';
                document.getElementById('avg-response').textContent = data.summary.average_response_time + ' ms';
            }
            
            // Update charts if detailed data is available
            if (data.detailed && data.detailed.uptime_trend) {
                const labels = data.detailed.uptime_trend.map(item => item.date);
                const uptimeData = data.detailed.uptime_trend.map(item => item.uptime_percentage);
                const responseTimeData = data.detailed.uptime_trend.map(item => item.average_response_time || 0);
                
                // Update uptime chart
                this.uptimeChart.data.labels = labels;
                this.uptimeChart.data.datasets[0].data = uptimeData;
                this.uptimeChart.update();
                
                // Update response time chart
                this.responseTimeChart.data.labels = labels;
                this.responseTimeChart.data.datasets[0].data = responseTimeData;
                this.responseTimeChart.update();
            }
            
            // Update data table
            this.updateDataTable(data);
        }
        
        updateDataTable(data) {
            const tbody = document.querySelector('#report-data-table tbody');
            tbody.innerHTML = ''; // Clear existing data
            
            if (!data.detailed || !data.detailed.top_issue_devices) {
                // If no detailed data, show a message
                const row = document.createElement('tr');
                row.innerHTML = '<td colspan="6" class="px-6 py-4 text-center text-gray-500">No data available</td>';
                tbody.appendChild(row);
                return;
            }
            
            // Add data rows
            data.detailed.top_issue_devices.forEach((device, index) => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50';
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${device.name}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${device.alert_count || 0} alerts</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${device.average_response_time || 'N/A'} ms</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${device.max_response_time || 'N/A'} ms</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${device.min_response_time || 'N/A'} ms</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                            ${device.alert_count || 0}
                        </span>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }
        
        async generatePDF() {
            try {
                // Get parameters from UI
                const reportPeriod = document.getElementById('report-period').value;
                const deviceId = document.getElementById('device-select').value;
                
                // Build parameters for PDF generation
                let params = new URLSearchParams({
                    report_type: 'detailed',
                    period: reportPeriod
                });
                
                if (deviceId) {
                    params.append('device_id', deviceId);
                }
                
                // For custom date range
                if (reportPeriod === 'custom') {
                    const startDate = document.getElementById('start_date').value;
                    const endDate = document.getElementById('end_date').value;
                    
                    if (!startDate || !endDate) {
                        alert('Please select both start and end dates for custom range');
                        return;
                    }
                    
                    params.set('start_date', startDate);
                    params.set('end_date', endDate);
                }
                
                // For PDF export, we'll use a direct download approach
                const url = `/api/reports/generate?${params}&format=pdf`;
                window.open(url, '_blank');
                
            } catch (error) {
                console.error('Error generating PDF:', error);
                alert('Error generating PDF: ' + error.message);
            }
        }
    }
    
    // Initialize report manager when page loads
    document.addEventListener('DOMContentLoaded', function() {
        new ReportManager();
    });
</script>
@endpush
@endsection
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-xl shadow-card p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-blue-100 text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Devices</p>
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Device::count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-card p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-green-100 text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Uptime</p>
                    <p class="text-2xl font-bold text-gray-900">99.9%</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-card p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-purple-100 text-purple-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Avg. Response</p>
                    <p class="text-2xl font-bold text-gray-900">45ms</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-card p-6 border border-gray-200">
            <div class="flex items-center">
                <div class="p-3 rounded-lg bg-yellow-100 text-yellow-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Alerts</p>
                    <p class="text-2xl font-bold text-gray-900">{{ \App\Models\Alert::count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Uptime Chart -->
        <div class="bg-white rounded-xl shadow-card p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Uptime Overview</h3>
            <div class="h-80">
                <canvas id="uptimeChart"></canvas>
            </div>
        </div>

        <!-- Response Time Chart -->
        <div class="bg-white rounded-xl shadow-card p-6 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Response Time Trend</h3>
            <div class="h-80">
                <canvas id="responseTimeChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Device Performance Table -->
    <div class="bg-white rounded-xl shadow-card border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Device Performance Summary</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Device</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Uptime</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg. Response</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alerts</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse(\App\Models\Device::all() as $device)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                    <span class="text-blue-800 font-medium">{{ strtoupper(substr($device->name, 0, 1)) }}</span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $device->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $device->ip_address }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">99.9%</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $device->response_time }}ms</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $device->alerts()->count() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $device->status === 'up' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($device->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">No devices found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Uptime Chart
        const uptimeCtx = document.getElementById('uptimeChart').getContext('2d');
        new Chart(uptimeCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Uptime %',
                    data: [99.8, 99.9, 100, 99.7, 99.9, 99.8, 100, 99.9, 99.8, 99.9, 99.7, 99.8],
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 99
                    }
                }
            }
        });

        // Response Time Chart
        const responseTimeCtx = document.getElementById('responseTimeChart').getContext('2d');
        new Chart(responseTimeCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Response Time (ms)',
                    data: [45, 42, 38, 50, 47, 44, 39, 41, 43, 46, 48, 45],
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection