"""
Routers package for NetMonitor.
"""
from app.routers.auth import router as auth_router
from app.routers.devices import router as devices_router
from app.routers.alerts import router as alerts_router
from app.routers.dashboard import router as dashboard_router
from app.routers.discovery import router as discovery_router
from app.routers.reports import router as reports_router

__all__ = ["auth_router", "devices_router", "alerts_router", "dashboard_router", "discovery_router", "reports_router"]
