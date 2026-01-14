import sys
import os
from datetime import datetime

# Add parent directory to path for imports
sys.path.insert(0, os.path.dirname(os.path.dirname(os.path.abspath(__file__))))

from app.database import SessionLocal
from app.models import User
# passlib should be installed as it is used in init_db.py
from passlib.context import CryptContext


def create_user():
    email = "admin@wastukancana.ac.id"
    password = "admin12345"
    print(f"Adding user: {email}")
    
    db = SessionLocal()
    try:
        existing = db.query(User).filter(User.email == email).first()
        if existing:
            print("User already exists.")
            return

        # Use the static method from User model to hash password
        hashed = User.hash_password(password)
        
        user = User(
            first_name="Admin",
            last_name="Wastukancana",
            email=email,
            password=hashed,
            role="admin",
            created_at=datetime.utcnow(),
            updated_at=datetime.utcnow()
        )
        db.add(user)
        db.commit()
        print("User created successfully.")
    except Exception as e:
        print(f"Error: {e}")
        db.rollback()
    finally:
        db.close()

if __name__ == "__main__":
    create_user()
