<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Monitoring Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .summary-stats {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
            text-align: center;
        }
        .stat-box {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 5px;
            width: 20%;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .stat-label {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        .section {
            margin: 20px 0;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            border-bottom: 2px solid #333;
            padding-bottom: 5px;
            margin: 20px 0 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Monitoring Konektivitas</h1>
        <p>Periode: {{ $dateRange['start'] }} hingga {{ $dateRange['end'] }}</p>
        <p>Dibuat pada: {{ $generatedAt }}</p>
    </div>

    <div class="summary-stats">
        <div class="stat-box">
            <div class="stat-value">{{ count($devices) }}</div>
            <div class="stat-label">Total Perangkat</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ $totalLogs }}</div>
            <div class="stat-label">Total Pengecekan</div>
        </div>
        <div class="stat-box">
            <div class="stat-value" style="color: #4CAF50;">{{ $upLogs }}</div>
            <div class="stat-label">Status UP</div>
        </div>
        <div class="stat-box">
            <div class="stat-value" style="color: #F44336;">{{ $downLogs }}</div>
            <div class="stat-label">Status DOWN</div>
        </div>
        <div class="stat-box">
            <div class="stat-value">{{ $avgResponseTime }} ms</div>
            <div class="stat-label">Rata-rata Respon</div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">Daftar Perangkat</div>
        <table>
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>IP Address</th>
                    <th>Tipe</th>
                    <th>Lokasi</th>
                    <th>Status Terakhir</th>
                </tr>
            </thead>
            <tbody>
                @foreach($devices as $device)
                <tr>
                    <td>{{ $device->name }}</td>
                    <td>{{ $device->ip_address }}</td>
                    <td>{{ $device->type }}</td>
                    <td>{{ $device->location }}</td>
                    <td>
                        <span style="color: {{ $device->status === 'up' ? '#4CAF50' : ($device->status === 'down' ? '#F44336' : '#999') }};">
                            {{ $device->status }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Riwayat Peringatan</div>
        <table>
            <thead>
                <tr>
                    <th>Perangkat</th>
                    <th>Jenis</th>
                    <th>Status</th>
                    <th>Waktu</th>
                    <th>Pesan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($alerts as $alert)
                <tr>
                    <td>{{ $alert->device->name }}</td>
                    <td>{{ $alert->type === 'device_down' ? 'Perangkat Down' : 'Perangkat Up' }}</td>
                    <td>{{ $alert->status }}</td>
                    <td>{{ $alert->created_at->format('d M Y H:i:s') }}</td>
                    <td>{{ $alert->message }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center;">Tidak ada peringatan dalam periode ini</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Statistik Berdasarkan Perangkat</div>
        <table>
            <thead>
                <tr>
                    <th>Perangkat</th>
                    <th>Total Pengecekan</th>
                    <th>UP</th>
                    <th>DOWN</th>
                    <th>Rata-rata Respon (ms)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($devices as $device)
                    @php
                        $deviceLogs = $logs->where('device_id', $device->id);
                        $deviceUpLogs = $deviceLogs->where('status', 'up')->count();
                        $deviceDownLogs = $deviceLogs->where('status', 'down')->count();
                        $deviceAvgResponseTime = $deviceLogs->avg('response_time') ?? 0;
                    @endphp
                    <tr>
                        <td>{{ $device->name }}</td>
                        <td>{{ $deviceLogs->count() }}</td>
                        <td style="color: #4CAF50;">{{ $deviceUpLogs }}</td>
                        <td style="color: #F44336;">{{ $deviceDownLogs }}</td>
                        <td>{{ round($deviceAvgResponseTime, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh Sistem Monitoring Konektivitas STT Wastukancana</p>
        <p>Halaman <span class="page"></span></p>
    </div>
</body>
</html>