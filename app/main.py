"""
NetMonitor FastAPI Application
Main entry point for the API server.
"""
from fastapi import FastAPI, Request
from fastapi.middleware.cors import CORSMiddleware
from fastapi.responses import JSONResponse
from contextlib import asynccontextmanager
import time

from app.config import settings
from app.database import engine, Base
from app.routers import auth_router, devices_router, alerts_router, dashboard_router, discovery_router, reports_router


@asynccontextmanager
async def lifespan(app: FastAPI):
    """Application lifespan events."""
    # Startup
    print(f"🚀 Starting {settings.app_name} API server...")
    print(f"📊 Environment: {settings.app_env}")
    print(f"🔗 Database: {settings.db_host}:{settings.db_port}/{settings.db_name}")
    yield
    # Shutdown
    print(f"👋 Shutting down {settings.app_name} API server...")


# Create FastAPI application
app = FastAPI(
    title=settings.app_name,
    description="Network Monitoring System API",
    version="1.0.0",
    docs_url="/docs",
    redoc_url="/redoc",
    lifespan=lifespan
)

# CORS middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # In production, specify allowed origins
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)


# Request timing middleware
@app.middleware("http")
async def add_process_time_header(request: Request, call_next):
    start_time = time.time()
    response = await call_next(request)
    process_time = time.time() - start_time
    response.headers["X-Process-Time"] = str(round(process_time * 1000, 2))
    return response


# Global exception handler
@app.exception_handler(Exception)
async def global_exception_handler(request: Request, exc: Exception):
    return JSONResponse(
        status_code=500,
        content={
            "success": False,
            "message": "Internal server error",
            "detail": str(exc) if settings.debug else None
        }
    )


# Register routers
app.include_router(auth_router)
app.include_router(devices_router)
app.include_router(alerts_router)
app.include_router(dashboard_router)
app.include_router(discovery_router)
app.include_router(reports_router)


# Health check endpoints
@app.get("/", tags=["Health"])
async def root():
    """Root endpoint."""
    return {
        "success": True,
        "message": f"Welcome to {settings.app_name} API",
        "version": "1.0.0"
    }


@app.get("/health", tags=["Health"])
async def health_check():
    """Health check endpoint."""
    from app.database import check_database_connection
    
    db_status = check_database_connection()
    
    return {
        "success": True,
        "status": "healthy" if db_status else "degraded",
        "components": {
            "api": "healthy",
            "database": "healthy" if db_status else "unhealthy"
        }
    }


@app.get("/api/health", tags=["Health"])
async def api_health():
    """API health check endpoint."""
    return {
        "success": True,
        "message": "API is running",
        "timestamp": time.time()
    }


if __name__ == "__main__":
    import uvicorn
    uvicorn.run(
        "app.main:app",
        host=settings.api_host,
        port=settings.api_port,
        reload=settings.debug
    )
