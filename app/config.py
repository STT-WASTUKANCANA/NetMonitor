"""
NetMonitor Application Configuration
"""
import os
from functools import lru_cache
from pydantic_settings import BaseSettings, SettingsConfigDict
from dotenv import load_dotenv

load_dotenv()


class Settings(BaseSettings):
    """Application settings loaded from environment variables."""
    
    # Application
    app_name: str = "NetMonitor"
    app_env: str = "development"
    debug: bool = True
    
    # API
    api_host: str = "0.0.0.0"
    api_port: int = 8001
    secret_key: str = "your-secret-key-change-in-production"
    
    # Database
    db_host: str = "127.0.0.1"
    db_port: int = 3306
    db_name: str = "netmonitor"
    db_user: str = "root"
    db_password: str = ""
    
    # JWT
    jwt_secret_key: str = "your-jwt-secret-key-change-in-production"
    jwt_algorithm: str = "HS256"
    jwt_access_token_expire_minutes: int = 1440
    
    # Streamlit (optional, used by streamlit_app)
    streamlit_port: int = 8501
    api_base_url: str = "http://localhost:8001"
    
    # Monitoring (Real-time: 30 seconds)
    monitor_interval: int = 30
    monitor_timeout: int = 10
    
    # Timezone (GMT+7 Jakarta/WIB)
    timezone: str = "Asia/Jakarta"
    
    @property
    def database_url(self) -> str:
        """Generate database URL for SQLAlchemy."""
        return f"mysql+pymysql://{self.db_user}:{self.db_password}@{self.db_host}:{self.db_port}/{self.db_name}"
    
    model_config = SettingsConfigDict(
        env_file=".env",
        env_file_encoding="utf-8",
        case_sensitive=False,
        extra="ignore"  # Allow extra fields in .env
    )


def get_settings() -> Settings:
    """Get settings instance (no caching for fresh .env reload)."""
    return Settings()


settings = get_settings()

