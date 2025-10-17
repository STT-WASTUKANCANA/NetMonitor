# Skrip Monitoring Jaringan

Skrip Python ini memonitor perangkat jaringan dan melaporkan statusnya ke sistem monitoring Laravel.

## Persyaratan

- Python 3.6+
- Library requests

## Instalasi

1. Instal paket Python yang diperlukan:
   ```bash
   pip install requests
   ```

## Konfigurasi

Skrip dapat dikonfigurasi menggunakan variabel lingkungan:

- `API_BASE_URL`: URL dasar aplikasi Laravel (bawaan: http://localhost:8000)
- `API_TOKEN`: Token API opsional untuk otentikasi

Contoh:
```bash
export API_BASE_URL="https://your-monitoring-system.com"
export API_TOKEN="your-api-token-here"
```

## Penggunaan

Jalankan skrip monitoring:
```bash
python3 monitor.py
```

## Pengaturan sebagai Cron Job

Untuk menjalankan monitoring secara otomatis setiap 5 menit, tambahkan baris ini ke crontab Anda:
```bash
*/5 * * * * cd /path/to/your/project && python3 scripts/monitor.py >> logs/monitor.log 2>&1
```

Ini akan menjalankan skrip setiap 5 menit dan mencatat output ke `logs/monitor.log`.