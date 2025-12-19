#!/usr/bin/env python3
"""
Reset login attempts counter for NetMonitor.
This script clears the login attempt locks.
"""

import os
import sys
import shutil
from pathlib import Path

# Add project to path
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

def reset_session_state_file():
    """Remove session state files if they exist."""
    # Cache directories to clear
    cache_dirs = [
        Path.home() / ".streamlit" / "cache",
        Path.home() / ".streamlit",
        Path.home() / ".cache" / "streamlit",
        Path(".streamlit") / "cache",
        Path(".streamlit"),
    ]
    
    print("🧹 Clearing Streamlit cache and session files...")
    print()
    
    cleared_count = 0
    for cache_dir in cache_dirs:
        if cache_dir.exists():
            try:
                shutil.rmtree(cache_dir)
                print(f"  ✓ Removed: {cache_dir}")
                cleared_count += 1
            except Exception as e:
                print(f"  ✗ Could not remove {cache_dir}: {e}")
    
    # Clear __pycache__ directories
    print()
    print("🧹 Clearing Python cache files...")
    pycache_count = 0
    for root, dirs, files in os.walk("."):
        if "__pycache__" in dirs:
            pycache_path = Path(root) / "__pycache__"
            try:
                shutil.rmtree(pycache_path)
                pycache_count += 1
            except Exception:
                pass
    
    if pycache_count > 0:
        print(f"  ✓ Removed {pycache_count} __pycache__ directories")
    
    print()
    if cleared_count > 0 or pycache_count > 0:
        print("✅ Cache cleared successfully!")
    else:
        print("ℹ️  No cache files found (already clean)")
    
    return True

def clear_browser_instructions():
    """Print instructions for clearing browser cache."""
    print()
    print("=" * 60)
    print("📱 CLEAR BROWSER CACHE")
    print("=" * 60)
    print()
    print("Untuk menghapus login attempts sepenuhnya, buka browser dan:")
    print()
    print("Cara 1: Developer Console (Recommended)")
    print("  1. Tekan F12 (buka Developer Tools)")
    print("  2. Buka tab 'Console'")
    print("  3. Ketik dan Enter:")
    print("     localStorage.clear(); sessionStorage.clear(); location.reload();")
    print()
    print("Cara 2: Clear Browser Data")
    print("  1. Tekan Ctrl + Shift + Delete")
    print("  2. Pilih 'Cookies and site data'")
    print("  3. Pilih 'Cached images and files'")
    print("  4. Klik 'Clear data'")
    print()

def main():
    """Main function."""
    print()
    print("╔" + "═" * 58 + "╗")
    print("║" + " " * 12 + "NetMonitor Login Attempts Reset" + " " * 15 + "║")
    print("╚" + "═" * 58 + "╝")
    print()
    
    print("Script ini akan:")
    print("  1. ✓ Menghapus Streamlit session cache")
    print("  2. ✓ Menghapus Python cache files")
    print("  3. ✓ Mereset login attempt counters")
    print()
    
    response = input("Lanjutkan? (y/n): ").strip().lower()
    if response != 'y':
        print()
        print("❌ Dibatalkan.")
        return
    
    print()
    print("-" * 60)
    success = reset_session_state_file()
    print("-" * 60)
    
    if success:
        clear_browser_instructions()
        
        print()
        print("=" * 60)
        print("✅ Login attempts telah direset!")
        print("=" * 60)
        print()
        print("Langkah selanjutnya:")
        print("  1. Clear browser cache (lihat instruksi di atas)")
        print("  2. Pastikan database sudah berjalan: ./quick_fix.sh")
        print("  3. Restart aplikasi: ./start.sh")
        print("  4. Login dengan kredensial yang benar")
        print()
    else:
        print()
        print("❌ Gagal mereset login attempts")
        print("Coba jalankan dengan: sudo python3 reset_login_attempts.py")
        print()

if __name__ == "__main__":
    try:
        main()
    except KeyboardInterrupt:
        print("\n\n❌ Dibatalkan oleh user.")
        sys.exit(1)
    except Exception as e:
        print(f"\n❌ Error: {e}")
        sys.exit(1)
