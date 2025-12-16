"""
API Client for Streamlit to communicate with FastAPI backend.
"""
import requests
from typing import Optional, Dict, Any
from streamlit_app.config import config


class APIClient:
    """HTTP client for API communication."""
    
    def __init__(self, base_url: str = None, token: str = None):
        self.base_url = base_url or config.API_BASE_URL
        self.token = token
    
    def set_token(self, token: str):
        """Set authentication token."""
        self.token = token
    
    def _headers(self) -> Dict[str, str]:
        """Get request headers with authentication."""
        headers = {
            "Content-Type": "application/json",
            "Accept": "application/json"
        }
        if self.token:
            headers["Authorization"] = f"Bearer {self.token}"
        return headers
    
    def _request(
        self,
        method: str,
        endpoint: str,
        data: Optional[Dict] = None,
        params: Optional[Dict] = None
    ) -> Dict[str, Any]:
        """Make HTTP request to API."""
        url = f"{self.base_url}{endpoint}"
        
        try:
            response = requests.request(
                method=method,
                url=url,
                headers=self._headers(),
                json=data,
                params=params,
                timeout=30
            )
            
            if response.status_code == 401:
                return {"success": False, "message": "Unauthorized", "status_code": 401}
            
            if response.status_code == 404:
                return {"success": False, "message": "Not found", "status_code": 404}
            
            return response.json()
        
        except requests.exceptions.ConnectionError:
            return {"success": False, "message": "Could not connect to API server"}
        except requests.exceptions.Timeout:
            return {"success": False, "message": "Request timed out"}
        except Exception as e:
            return {"success": False, "message": str(e)}
    
    def get(self, endpoint: str, params: Optional[Dict] = None) -> Dict:
        """GET request."""
        return self._request("GET", endpoint, params=params)
    
    def post(self, endpoint: str, data: Optional[Dict] = None) -> Dict:
        """POST request."""
        return self._request("POST", endpoint, data=data)
    
    def put(self, endpoint: str, data: Optional[Dict] = None) -> Dict:
        """PUT request."""
        return self._request("PUT", endpoint, data=data)
    
    def patch(self, endpoint: str, data: Optional[Dict] = None) -> Dict:
        """PATCH request."""
        return self._request("PATCH", endpoint, data=data)
    
    def delete(self, endpoint: str) -> Dict:
        """DELETE request."""
        return self._request("DELETE", endpoint)
    
    # Auth endpoints
    def login(self, email: str, password: str) -> Dict:
        """Login and get token."""
        return self.post("/api/auth/login", {"email": email, "password": password})
    
    def logout(self) -> Dict:
        """Logout."""
        return self.post("/api/auth/logout")
    
    def get_user(self) -> Dict:
        """Get current user."""
        return self.get("/api/auth/user")
    
    # Device endpoints
    def get_devices(self, **params) -> Dict:
        """Get devices list."""
        return self.get("/api/devices", params=params)
    
    def get_device(self, device_id: int) -> Dict:
        """Get single device."""
        return self.get(f"/api/devices/{device_id}")
    
    def create_device(self, data: Dict) -> Dict:
        """Create device."""
        return self.post("/api/devices", data)
    
    def update_device(self, device_id: int, data: Dict) -> Dict:
        """Update device."""
        return self.put(f"/api/devices/{device_id}", data)
    
    def delete_device(self, device_id: int) -> Dict:
        """Delete device."""
        return self.delete(f"/api/devices/{device_id}")
    
    def get_device_logs(self, device_id: int, **params) -> Dict:
        """Get device logs."""
        return self.get(f"/api/devices/{device_id}/logs", params=params)
    
    # Alert endpoints
    def get_alerts(self, **params) -> Dict:
        """Get alerts list."""
        return self.get("/api/alerts", params=params)
    
    def get_alert(self, alert_id: int) -> Dict:
        """Get single alert."""
        return self.get(f"/api/alerts/{alert_id}")
    
    def update_alert(self, alert_id: int, data: Dict) -> Dict:
        """Update alert status."""
        return self.patch(f"/api/alerts/{alert_id}", data)
    
    def bulk_update_alerts(self, alert_ids: list, status: str) -> Dict:
        """Bulk update alerts."""
        return self.post("/api/alerts/bulk-update", {"alert_ids": alert_ids, "status": status})
    
    # Dashboard endpoints
    def get_dashboard_summary(self) -> Dict:
        """Get dashboard summary."""
        return self.get("/api/dashboard/summary")
    
    def get_dashboard_metrics(self, period: str = "7d", device_id: int = None) -> Dict:
        """Get dashboard metrics."""
        params = {"period": period}
        if device_id:
            params["device_id"] = device_id
        return self.get("/api/dashboard/metrics", params=params)
    
    def get_recent_alerts(self, limit: int = 10) -> Dict:
        """Get recent alerts."""
        return self.get("/api/dashboard/recent-alerts", params={"limit": limit})
    
    def get_device_hierarchy(self) -> Dict:
        """Get device hierarchy."""
        return self.get("/api/dashboard/device-hierarchy")
    
    # Report endpoints
    def get_report_data(self, period: str, start_date: str = None, end_date: str = None) -> Dict:
        """Get report data."""
        params = {"period": period}
        if start_date:
            params["start_date"] = start_date
        if end_date:
            params["end_date"] = end_date
        return self.get("/api/reports/data", params=params)
    
    # Health check
    def health_check(self) -> Dict:
        """Check API health."""
        return self.get("/health")


# Global API client instance
api_client = APIClient()
