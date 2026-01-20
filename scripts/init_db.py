#!/usr/bin/env python3
"""
Database initialization script using SQLAlchemy ORM.
Creates all tables and optionally seeds with an admin user.
"""

import sys
import os

# Add parent directory to path for imports
sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

from sqlalchemy import text
from passlib.context import CryptContext
from datetime import datetime

from app.database import engine, Base, SessionLocal
from app.models import User, Device, DeviceLog, Alert

# Password hashing
pwd_context = CryptContext(schemes=["bcrypt"], deprecated="auto")


def create_tables():
    """Create all tables defined in the models."""
    print("🔧 Creating database tables...")
    
    try:
        # Import all models to register them with Base
        Base.metadata.create_all(bind=engine)
        print("✅ All tables created successfully!")
        return True
    except Exception as e:
        print(f"❌ Error creating tables: {e}")
        return False


def create_admin_user(email: str = "admin@netmonitor.local", password: str = "password123"):
    """Create an admin user if it doesn't exist."""
    print(f"\n👤 Checking for admin user ({email})...")
    
    db = SessionLocal()
    try:
        # Check if user exists
        existing_user = db.query(User).filter(User.email == email).first()
        
        if existing_user:
            print(f"ℹ️  User '{email}' already exists (ID: {existing_user.id})")
            return existing_user.id
        
        # Create new admin user
        hashed_password = pwd_context.hash(password)
        
        new_user = User(
            first_name="Admin",
            last_name="NetMonitor",
            email=email,
            password=hashed_password,
            role="admin",
            created_at=datetime.utcnow(),
            updated_at=datetime.utcnow()
        )
        
        db.add(new_user)
        db.commit()
        db.refresh(new_user)
        
        print(f"✅ Admin user created successfully!")
        print(f"   Email: {email}")
        print(f"   Password: {password}")
        print(f"   User ID: {new_user.id}")
        
        return new_user.id
        
    except Exception as e:
        db.rollback()
        print(f"❌ Error creating admin user: {e}")
        return None
    finally:
        db.close()


def show_tables():
    """Show all tables in the database."""
    print("\n📋 Database tables:")
    
    db = SessionLocal()
    try:
        result = db.execute(text("SHOW TABLES"))
        tables = result.fetchall()
        
        if tables:
            for table in tables:
                print(f"   • {table[0]}")
        else:
            print("   (no tables found)")
            
    except Exception as e:
        print(f"❌ Error listing tables: {e}")
    finally:
        db.close()


def test_connection():
    """Test database connection."""
    print("🔌 Testing database connection...")
    
    db = SessionLocal()
    try:
        result = db.execute(text("SELECT 1"))
        result.fetchone()
        print("✅ Database connection successful!")
        
        # Show connection info
        result = db.execute(text("SELECT DATABASE()"))
        db_name = result.fetchone()[0]
        print(f"   Database: {db_name}")
        
        return True
    except Exception as e:
        print(f"❌ Database connection failed: {e}")
        return False
    finally:
        db.close()


def main():
    """Main function to initialize the database."""
    print("=" * 50)
    print("🚀 NetMonitor Database Initialization")
    print("=" * 50)
    
    # Test connection
    if not test_connection():
        print("\n❌ Cannot proceed without database connection.")
        print("   Please check your .env file and ensure MySQL is running.")
        sys.exit(1)
    
    # Create tables
    if not create_tables():
        print("\n❌ Failed to create tables.")
        sys.exit(1)
    
    # Show tables
    show_tables()
    
    # Create admin user
    create_admin_user()
    
    print("\n" + "=" * 50)
    print("✅ Database initialization complete!")
    print("=" * 50)
    print("\nNext steps:")
    print("1. Start FastAPI: uvicorn app.main:app --host 0.0.0.0 --port 8001 --reload")
    print("2. Start Streamlit: cd streamlit_app && streamlit run app.py --server.port 8501")
    print("3. Run monitor: sudo ../venv/bin/python scripts/monitor.py")


if __name__ == "__main__":
    main()
