# Network Monitoring Script

This Python script monitors network devices and reports their status to the Laravel monitoring system.

## Requirements

- Python 3.6+
- requests library

## Installation

1. Install required Python packages:
   ```bash
   pip install requests
   ```

## Configuration

The script can be configured using environment variables:

- `API_BASE_URL`: Base URL of the Laravel application (default: http://localhost:8000)
- `API_TOKEN`: Optional API token for authentication

Example:
```bash
export API_BASE_URL="https://your-monitoring-system.com"
export API_TOKEN="your-api-token-here"
```

## Usage

Run the monitoring script:
```bash
python3 monitor.py
```

## Setting up as a Cron Job

To run the monitoring automatically every 5 minutes, add this line to your crontab:
```bash
*/5 * * * * cd /path/to/your/project && python3 scripts/monitor.py >> logs/monitor.log 2>&1
```

This will run the script every 5 minutes and log output to `logs/monitor.log`.