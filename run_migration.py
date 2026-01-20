#!/usr/bin/env python3
"""
Script untuk menjalankan database migration dengan konfigurasi yang benar.
"""
import os
import sys
from pathlib import Path

# Set working directory to script location
os.chdir(Path(__file__).parent)

print("=" * 70)
print("NetMonitor Database Migration Script")
print("=" * 70)
print()

# Force reload environment variables
from dotenv import load_dotenv
load_dotenv(override=True)

# Load configuration
from app.config import settings

print("📋 Database Configuration:")
print(f"   Host: {settings.db_host}")
print(f"   Port: {settings.db_port}")
print(f"   Database: {settings.db_name}")
print(f"   User: {settings.db_user}")
print(f"   Connection: {settings.database_url}")
print()

# Test connection first
print("🔍 Testing database connection...")
try:
    import pymysql
    conn = pymysql.connect(
        host=settings.db_host,
        port=settings.db_port,
        user=settings.db_user,
        password=settings.db_password,
        database=settings.db_name,
        connect_timeout=5
    )
    
    cursor = conn.cursor()
    cursor.execute("SELECT VERSION(), DATABASE()")
    version, db = cursor.fetchone()
    print(f"   ✓ Connected to MySQL {version}")
    print(f"   ✓ Current database: {db}")
    cursor.close()
    conn.close()
    
except Exception as e:
    print(f"   ✗ Connection failed: {e}")
    print()
    print("Please check:")
    print("  1. MySQL is running on port 3307")
    print("  2. Database 'NetMonitor' exists")
    print("  3. User 'root' has access")
    sys.exit(1)

print()

# Check current database state
print("🔍 Checking database state...")
try:
    from app.database import engine
    from sqlalchemy import inspect, text
    
    inspector = inspect(engine)
    existing_tables = inspector.get_table_names()
    
    if existing_tables:
        print(f"   Found {len(existing_tables)} existing tables:")
        for table in existing_tables:
            print(f"     - {table}")
    else:
        print("   No tables found (fresh database)")
    
    # Check alembic_version
    with engine.connect() as conn:
        try:
            result = conn.execute(text("SELECT version_num FROM alembic_version"))
            current_version = result.scalar()
            if current_version:
                print(f"   Current migration version: {current_version}")
            else:
                print("   No migration version found")
        except:
            print("   No alembic_version table found")
    
except Exception as e:
    print(f"   Warning: Could not check database state: {e}")

print()

# Run migrations
print("🚀 Running database migrations...")
print()

try:
    from alembic.config import Config
    from alembic import command
    
    # Create Alembic configuration
    alembic_cfg = Config("alembic.ini")
    
    # Run upgrade to head
    command.upgrade(alembic_cfg, "head")
    
    print()
    print("=" * 70)
    print("✅ Migration completed successfully!")
    print("=" * 70)
    print()
    
    # Show final state
    print("📊 Final database state:")
    inspector = inspect(engine)
    tables = inspector.get_table_names()
    print(f"   Total tables: {len(tables)}")
    for table in sorted(tables):
        print(f"     - {table}")
    
    print()
    print("✓ Database is ready to use!")
    print()
    
except Exception as e:
    print()
    print("=" * 70)
    print("✗ Migration failed!")
    print("=" * 70)
    print(f"Error: {e}")
    print()
    import traceback
    traceback.print_exc()
    sys.exit(1)
