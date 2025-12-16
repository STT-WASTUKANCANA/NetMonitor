# 📋 Konvensi Kode - NetMonitor

Panduan coding standards untuk **NetMonitor** dengan Python/FastAPI/Streamlit.

---

## 🐍 Python Style Guide

Mengikuti **PEP 8** dengan beberapa penyesuaian.

### General Rules

```python
# Indentation: 4 spaces
def function_name():
    if condition:
        do_something()

# Max line length: 100 characters
# Prefer shorter lines when possible

# Imports order:
# 1. Standard library
# 2. Third-party
# 3. Local application
import os
import sys
from datetime import datetime

import requests
from fastapi import FastAPI
from sqlalchemy import Column

from app.models import Device
from app.config import settings
```

---

## 📁 Naming Conventions

### Files & Directories

| Type | Convention | Example |
|------|------------|---------|
| Python modules | snake_case | `device_log.py` |
| Directories | snake_case | `app/models/` |
| Streamlit pages | emoji + name | `1_🏠_Dashboard.py` |

### Code Elements

| Type | Convention | Example |
|------|------------|---------|
| Classes | PascalCase | `DeviceLog` |
| Functions | snake_case | `get_device_logs()` |
| Variables | snake_case | `device_count` |
| Constants | UPPER_SNAKE | `API_BASE_URL` |
| Private | _prefix | `_calculate_uptime()` |

---

## 🏗️ FastAPI Patterns

### Router Structure

```python
from fastapi import APIRouter, Depends, HTTPException, status

router = APIRouter(prefix="/api/devices", tags=["Devices"])

@router.get("", response_model=dict)
async def list_devices(
    status: Optional[str] = Query(None),
    db: Session = Depends(get_db),
    current_user: User = Depends(get_current_user)
):
    """Docstring explaining the endpoint."""
    # Implementation
    return {"success": True, "data": result}
```

### Response Format

Always use consistent response format:

```python
# Success
{"success": True, "data": {...}}
{"success": True, "message": "...", "data": {...}}

# Error
{"success": False, "message": "Error description"}
```

### Error Handling

```python
from fastapi import HTTPException, status

if not device:
    raise HTTPException(
        status_code=status.HTTP_404_NOT_FOUND,
        detail="Device not found"
    )
```

---

## 📊 SQLAlchemy Patterns

### Model Definition

```python
from sqlalchemy import Column, BigInteger, String, Enum, ForeignKey
from sqlalchemy.orm import relationship
from app.database import Base

class Device(Base):
    __tablename__ = "devices"
    
    # Primary key
    id = Column(BigInteger, primary_key=True, autoincrement=True)
    
    # Required fields
    name = Column(String(255), nullable=False)
    
    # Optional fields
    description = Column(Text, nullable=True)
    
    # Enums
    status = Column(Enum('up', 'down', 'unknown'), default='unknown')
    
    # Foreign keys
    parent_id = Column(BigInteger, ForeignKey('devices.id', ondelete='CASCADE'))
    
    # Relationships
    parent = relationship("Device", remote_side=[id], back_populates="children")
    children = relationship("Device", back_populates="parent")
    
    # Helper methods
    def to_dict(self) -> dict:
        return {...}
```

### Query Patterns

```python
# Single item
device = db.query(Device).filter(Device.id == device_id).first()

# List with filters
devices = db.query(Device).filter(
    Device.status == 'up',
    Device.type == 'router'
).all()

# Pagination
devices = db.query(Device).offset((page-1)*per_page).limit(per_page).all()

# Aggregation
from sqlalchemy import func
count = db.query(func.count(Device.id)).filter(Device.status == 'up').scalar()
```

---

## 🎨 Streamlit Patterns

### Page Structure

```python
import streamlit as st
from streamlit_app.utils import require_auth, api_client

def setup_page():
    st.set_page_config(page_title="...", layout="wide")

def render_sidebar():
    with st.sidebar:
        # Sidebar content
        pass

def main():
    require_auth()  # Check authentication first
    setup_page()
    render_sidebar()
    
    # Page content
    st.title("Page Title")
    
if __name__ == "__main__":
    main()
```

### Component Patterns

```python
# Metric cards using containers
with st.container():
    col1, col2, col3 = st.columns(3)
    with col1:
        st.metric("Label", "Value")

# Data display
df = pd.DataFrame(data)
st.dataframe(df, use_container_width=True)

# Charts
fig = px.line(df, x='date', y='value')
st.plotly_chart(fig, use_container_width=True)
```

---

## 📝 Documentation Standards

### Docstrings (Google Style)

```python
def update_device_status(
    device_id: int,
    status: str,
    response_time: Optional[float]
) -> bool:
    """
    Update device status from monitoring script.
    
    Args:
        device_id: The device ID to update
        status: New status ('up', 'down', 'unknown')
        response_time: Response time in milliseconds
        
    Returns:
        True if update successful, False otherwise
        
    Raises:
        HTTPException: If device not found
    """
    pass
```

### Comments

```python
# Single line comment for simple explanation

# Multi-line comment for
# more complex explanations
# that need more context

# TODO: Add feature X
# FIXME: Bug in calculation
# NOTE: Important consideration
```

---

## 🧪 Testing Standards

### Test Structure

```python
import pytest
from httpx import AsyncClient

class TestDeviceAPI:
    """Device API test cases."""
    
    @pytest.fixture
    def test_device(self):
        return {"name": "Test Device", "ip_address": "192.168.1.100"}
    
    async def test_create_device(self, client: AsyncClient, auth_token: str):
        response = await client.post(
            "/api/devices",
            headers={"Authorization": f"Bearer {auth_token}"},
            json={"name": "Test", "ip_address": "192.168.1.1", "type": "router"}
        )
        assert response.status_code == 201
        assert response.json()["success"] is True
```

---

## 🔐 Security Practices

```python
# Never hardcode secrets
SECRET_KEY = os.getenv("SECRET_KEY")  # ✅ Good
SECRET_KEY = "my-secret-key"          # ❌ Bad

# Always validate input with Pydantic
class DeviceCreate(BaseModel):
    name: str = Field(..., min_length=1, max_length=255)
    ip_address: str = Field(..., max_length=45)

# Use parameterized queries (SQLAlchemy handles this)
db.query(Device).filter(Device.id == device_id)  # ✅ Safe
```

---

**Versi**: 2.0  
**Terakhir Diperbarui**: 11 Desember 2025
