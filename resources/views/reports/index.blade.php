<x-app-layout>
    <x-slot name="header">
        <div class="mb-2">
            <!-- Judul Halaman -->
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Performance Reports') }}
            </h2>

            <!-- Breadcrumb -->
            <nav class="flex mt-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li class="inline-flex items-center">
                        <a href="{{ route('dashboard') }}"
                            class="inline-flex items-center text-sm font-medium text-gray-400 hover:text-blue-600 transition-colors">
                            <svg class="w-4 h-4 mr-2 text-gray-400 hover:text-blue-500 transition-colors" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path
                                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                            </svg>
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-black mx-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                    clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm font-medium text-black">Performance Reports</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="device-select" class="block text-sm font-medium text-gray-700 mb-1">
                                Pilih Perangkat
                            </label>

                            <select id="device-select" multiple class="w-full h-56 text-sm rounded-lg border border-gray-300 bg-white shadow-sm
                            focus:border-blue-100 focus:ring focus:ring-indigo-200 focus:ring-opacity-50
                            overflow-y-auto p-2 space-y-1">
                                @foreach($devices as $device)
                                    <option value="{{ $device->id }}"
                                        class="py-2 px-3 border-b border-gray-200 last:border-0 hover:bg-indigo-50">
                                        {{ $device->name }}
                                    </option>
                                @endforeach
                            </select>

                            <p class="mt-1 text-sm text-gray-500">
                                Pilih satu atau lebih perangkat untuk difilter
                            </p>
                        </div>

                        <div>
                            <label for="date-range" class="block text-sm font-medium text-gray-700 mb-1">Rentang
                                Waktu</label>
                            <select id="date-range"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="1">1 Hari</option>
                                <option value="7" selected>7 Hari</option>
                                <option value="30">30 Hari</option>
                                <option value="90">90 Hari</option>
                            </select>
                        </div>

                        <div class="flex items-end space-x-2">
                            <button id="refresh-charts"
                                class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Refresh Charts
                            </button>
                        </div>

                        <div class="flex items-end">
                            <button id="generate-pdf"
                                class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                Generate PDF Report
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Response Time Chart -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Tren Waktu Respons (ms)</h3>
                    <div class="h-80">
                        <canvas id="responseTimeChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Status Chart -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Tren Status Perangkat</h3>
                    <div class="h-80">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // Initialize charts
        let responseTimeChart, statusChart;

        // Fetch and update charts
        async function updateCharts() {
            // Get selected devices and date range
            const selectedDevices = Array.from(document.getElementById('device-select').selectedOptions)
                .map(option => option.value);
            const dateRange = document.getElementById('date-range').value;

            // Update response time chart
            await updateResponseTimeChart(selectedDevices, dateRange);

            // Update status chart
            await updateStatusChart(selectedDevices, dateRange);
        }

        async function updateResponseTimeChart(deviceIds, range) {
            try {
                const response = await fetch(`{{ route('reports.response-time') }}?${new URLSearchParams({
                    device_ids: deviceIds,
                    range: range
                })}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    const data = await response.json();

                    const ctx = document.getElementById('responseTimeChart').getContext('2d');

                    if (responseTimeChart) {
                        responseTimeChart.destroy();
                    }

                    responseTimeChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.dates,
                            datasets: [{
                                label: 'Waktu Respons Rata-rata (ms)',
                                data: data.response_times,
                                borderColor: 'rgb(75, 192, 192)',
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                tension: 0.1,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Waktu Respons (ms)'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Tanggal'
                                    }
                                }
                            }
                        }
                    });
                }
            } catch (error) {
                console.error('Error fetching response time data:', error);
            }
        }

        async function updateStatusChart(deviceIds, range) {
            try {
                const response = await fetch(`{{ route('reports.status') }}?${new URLSearchParams({
                    device_ids: deviceIds,
                    range: range
                })}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    const data = await response.json();

                    const ctx = document.getElementById('statusChart').getContext('2d');

                    if (statusChart) {
                        statusChart.destroy();
                    }

                    statusChart = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.dates,
                            datasets: [
                                {
                                    label: 'Status UP',
                                    data: data.up_counts,
                                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                                    borderColor: 'rgba(75, 192, 192, 1)',
                                    borderWidth: 1
                                },
                                {
                                    label: 'Status DOWN',
                                    data: data.down_counts,
                                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                                    borderColor: 'rgba(255, 99, 132, 1)',
                                    borderWidth: 1
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'Jumlah'
                                    }
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: 'Tanggal'
                                    }
                                }
                            }
                        }
                    });
                }
            } catch (error) {
                console.error('Error fetching status data:', error);
            }
        }

        // Setup event listeners
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize charts
            updateCharts();

            // Refresh button
            document.getElementById('refresh-charts').addEventListener('click', updateCharts);

            // Auto-refresh when filters change
            document.getElementById('device-select').addEventListener('change', updateCharts);
            document.getElementById('date-range').addEventListener('change', updateCharts);

            // PDF generation
            document.getElementById('generate-pdf').addEventListener('click', function () {
                // Get selected devices and date range
                const selectedDevices = Array.from(document.getElementById('device-select').selectedOptions)
                    .map(option => option.value);
                const dateRange = document.getElementById('date-range').value;

                // Build the query parameters
                const params = new URLSearchParams({
                    device_ids: selectedDevices,
                    range: dateRange
                });

                // Redirect to the PDF generation endpoint
                window.location.href = '{{ route('reports.pdf') }}?' + params.toString();
            });
        });
    </script>
</x-app-layout>