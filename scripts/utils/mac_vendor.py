#!/usr/bin/env python3
"""
MAC Vendor Lookup Module
Identifies device manufacturers from MAC addresses using OUI database
"""

import os
import json
import requests
from typing import Optional, Dict
from datetime import datetime, timedelta


class MACVendorLookup:
    """MAC address vendor lookup using OUI database"""
    
    def __init__(self, cache_file: str = 'oui_cache.json', cache_ttl_days: int = 30):
        """
        Initialize MAC vendor lookup.
        
        Args:
            cache_file: Path to cache file for OUI database
            cache_ttl_days: Cache time-to-live in days
        """
        self.cache_file = cache_file
        self.cache_ttl_days = cache_ttl_days
        self.oui_db = self._load_oui_database()
    
    def _load_oui_database(self) -> Dict[str, str]:
        """
        Load IEEE OUI database from cache or build basic database.
        
        Returns:
            Dictionary mapping OUI prefix to vendor name
        """
        # Check if cache exists and is recent
        if os.path.exists(self.cache_file):
            try:
                with open(self.cache_file, 'r') as f:
                    cache_data = json.load(f)
                
                cache_time = datetime.fromisoformat(cache_data.get('timestamp', '2000-01-01'))
                if datetime.now() - cache_time < timedelta(days=self.cache_ttl_days):
                    return cache_data.get('oui_db', {})
            except Exception:
                pass
        
        # Build basic OUI database with common vendors
        oui_db = self._get_basic_oui_database()
        
        # Save to cache
        try:
            with open(self.cache_file, 'w') as f:
                json.dump({
                    'timestamp': datetime.now().isoformat(),
                    'oui_db': oui_db
                }, f)
        except Exception:
            pass
        
        return oui_db
    
    def _get_basic_oui_database(self) -> Dict[str, str]:
        """
        Get basic OUI database with common network equipment vendors.
        
        Returns:
            Dictionary of OUI prefixes to vendor names
        """
        return {
            # Cisco
            '00:00:0C': 'Cisco Systems',
            '00:01:42': 'Cisco Systems',
            '00:01:43': 'Cisco Systems',
            '00:01:96': 'Cisco Systems',
            '00:01:97': 'Cisco Systems',
            '00:01:C7': 'Cisco Systems',
            '00:0D:BC': 'Cisco Systems',
            '00:0E:83': 'Cisco Systems',
            '00:1B:0D': 'Cisco Systems',
            '00:1C:0E': 'Cisco Systems',
            '00:1D:70': 'Cisco Systems',
            
            # Ubiquiti (UniFi)
            '00:15:6D': 'Ubiquiti Networks',
            '00:27:22': 'Ubiquiti Networks',
            '04:18:D6': 'Ubiquiti Networks',
            '24:A4:3C': 'Ubiquiti Networks',
            '68:72:51': 'Ubiquiti Networks',
            '74:83:C2': 'Ubiquiti Networks',
            '78:8A:20': 'Ubiquiti Networks',
            'B4:FB:E4': 'Ubiquiti Networks',
            'DC:9F:DB': 'Ubiquiti Networks',
            'F0:9F:C2': 'Ubiquiti Networks',
            
            # MikroTik
            '00:0C:42': 'MikroTik',
            '4C:5E:0C': 'MikroTik',
            '6C:3B:6B': 'MikroTik',
            'D4:CA:6D': 'MikroTik',
            'E4:8D:8C': 'MikroTik',
            
            # TP-Link
            '00:27:19': 'TP-Link',
            '10:FE:ED': 'TP-Link',
            '14:CF:92': 'TP-Link',
            '50:C7:BF': 'TP-Link',
            '98:DE:D0': 'TP-Link',
            'A4:2B:B0': 'TP-Link',
            'C0:4A:00': 'TP-Link',
            
            # D-Link
            '00:05:5D': 'D-Link',
            '00:0D:88': 'D-Link',
            '00:13:46': 'D-Link',
            '00:15:E9': 'D-Link',
            '00:17:9A': 'D-Link',
            '00:19:5B': 'D-Link',
            '00:1B:11': 'D-Link',
            '00:1C:F0': 'D-Link',
            
            # HP/HPE
            '00:10:83': 'HP',
            '00:11:0A': 'HP',
            '00:14:C2': 'HP',
            '00:17:A4': 'HP',
            '00:1A:4B': 'HP',
            '00:1E:0B': 'HP',
            '00:21:5A': 'HP',
            '00:23:7D': 'HP',
            
            # Netgear
            '00:09:5B': 'Netgear',
            '00:0F:B5': 'Netgear',
            '00:14:6C': 'Netgear',
            '00:18:4D': 'Netgear',
            '00:1B:2F': 'Netgear',
            '00:1E:2A': 'Netgear',
            '00:26:F2': 'Netgear',
            
            # Apple
            '00:03:93': 'Apple',
            '00:05:02': 'Apple',
            '00:0A:27': 'Apple',
            '00:0A:95': 'Apple',
            '00:0D:93': 'Apple',
            '00:16:CB': 'Apple',
            '00:17:F2': 'Apple',
            '00:19:E3': 'Apple',
            '00:1C:B3': 'Apple',
            '00:1E:52': 'Apple',
            '00:1F:5B': 'Apple',
            '00:1F:F3': 'Apple',
            '00:21:E9': 'Apple',
            '00:22:41': 'Apple',
            '00:23:12': 'Apple',
            '00:23:32': 'Apple',
            '00:23:6C': 'Apple',
            '00:23:DF': 'Apple',
            '00:24:36': 'Apple',
            '00:25:00': 'Apple',
            '00:25:4B': 'Apple',
            '00:25:BC': 'Apple',
            '00:26:08': 'Apple',
            '00:26:4A': 'Apple',
            '00:26:B0': 'Apple',
            '00:26:BB': 'Apple',
            
            # Dell
            '00:06:5B': 'Dell',
            '00:08:74': 'Dell',
            '00:0B:DB': 'Dell',
            '00:0D:56': 'Dell',
            '00:11:43': 'Dell',
            '00:12:3F': 'Dell',
            '00:13:72': 'Dell',
            '00:14:22': 'Dell',
            '00:15:C5': 'Dell',
            '00:18:8B': 'Dell',
            '00:19:B9': 'Dell',
            '00:1A:A0': 'Dell',
            '00:1C:23': 'Dell',
            '00:1D:09': 'Dell',
            
            # Raspberry Pi
            'B8:27:EB': 'Raspberry Pi Foundation',
            'DC:A6:32': 'Raspberry Pi Foundation',
            'E4:5F:01': 'Raspberry Pi Foundation',
            
            # Synology (NAS)
            '00:11:32': 'Synology',
            
            # QNAP (NAS)
            '00:08:9B': 'QNAP Systems',
            '24:5E:BE': 'QNAP Systems',
            
            # Hikvision (Cameras)
            '00:12:12': 'Hikvision',
            '44:19:B6': 'Hikvision',
            
            # Samsung
            '00:00:F0': 'Samsung',
            '00:12:47': 'Samsung',
            '00:13:77': 'Samsung',
            '00:15:B9': 'Samsung',
            '00:16:32': 'Samsung',
            '00:16:6B': 'Samsung',
            '00:16:6C': 'Samsung',
            '00:17:C9': 'Samsung',
            '00:17:D5': 'Samsung',
            '00:18:AF': 'Samsung',
        }
    
    def lookup(self, mac_address: str) -> Optional[str]:
        """
        Lookup vendor from MAC address.
        
        Args:
            mac_address: MAC address in format AA:BB:CC:DD:EE:FF or AA-BB-CC-DD-EE-FF
        
        Returns:
            Vendor name or None if not found
        """
        if not mac_address:
            return None
        
        # Normalize MAC address format
        mac = mac_address.upper().replace('-', ':')
        
        # Extract OUI (first 3 octets)
        oui = ':'.join(mac.split(':')[:3])
        
        # Lookup in database
        return self.oui_db.get(oui)
    
    def get_vendor_type_hint(self, vendor: Optional[str]) -> Optional[str]:
        """
        Get device type hint based on vendor name.
        
        Args:
            vendor: Vendor name
        
        Returns:
            Device type hint or None
        """
        if not vendor:
            return None
        
        vendor_lower = vendor.lower()
        
        # Network infrastructure vendors
        if any(v in vendor_lower for v in ['cisco', 'mikrotik', 'ubiquiti', 'juniper', 'aruba']):
            return 'network_infrastructure'
        
        # NAS vendors
        if any(v in vendor_lower for v in ['synology', 'qnap', 'netgear nas']):
            return 'nas'
        
        # Camera vendors
        if any(v in vendor_lower for v in ['hikvision', 'dahua', 'axis']):
            return 'camera'
        
        # Printer vendors
        if any(v in vendor_lower for v in ['hp', 'canon', 'epson', 'brother']):
            return 'printer'
        
        # IoT/Embedded vendors
        if any(v in vendor_lower for v in ['raspberry pi', 'arduino', 'espressif']):
            return 'iot'
        
        # Computer vendors
        if any(v in vendor_lower for v in ['apple', 'dell', 'lenovo', 'asus', 'acer']):
            return 'computer'
        
        # Mobile vendors
        if any(v in vendor_lower for v in ['samsung', 'xiaomi', 'huawei', 'oppo']):
            return 'mobile'
        
        return None
