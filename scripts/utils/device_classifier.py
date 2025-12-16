#!/usr/bin/env python3
"""
Device Classifier Module
Enhanced device classification using multiple factors
"""

from typing import List, Dict, Optional, NamedTuple
from dataclasses import dataclass


@dataclass
class DeviceClassification:
    """Device classification result"""
    device_type: str
    hierarchy: str
    confidence: int
    method: str
    reasoning: List[str]


class DeviceClassifier:
    """Enhanced device classification using multi-factor analysis"""
    
    # Port signatures for different device types
    PORT_SIGNATURES = {
        'router': {
            'required': [],
            'common': [22, 23, 80, 443, 53, 161],
            'indicators': [53, 67, 547],  # DNS, DHCP
        },
        'switch': {
            'required': [],
            'common': [22, 23, 80, 443, 161],
            'indicators': [161, 22],  # SNMP, SSH
        },
        'access_point': {
            'required': [],
            'common': [80, 443, 161],
            'indicators': [161, 10001],  # SNMP, Ubiquiti discovery
        },
        'server': {
            'required': [],
            'common': [22, 80, 443],
            'indicators': [3306, 5432, 6379, 27017, 9000, 8080],  # Database/app ports
        },
        'nas': {
            'required': [],
            'common': [22, 80, 443, 445, 548, 873, 2049],
            'indicators': [445, 548, 2049],  # SMB, AFP, NFS
        },
        'printer': {
            'required': [],
            'common': [9100, 631, 515],
            'indicators': [9100, 631, 515],  # JetDirect, IPP, LPD
        },
        'camera': {
            'required': [],
            'common': [80, 554, 8000, 37777],
            'indicators': [554, 37777, 8000],  # RTSP, camera-specific
        },
        'desktop': {
            'required': [],
            'common': [135, 139, 445, 3389],
            'indicators': [135, 139, 445],  # Windows RPC, NetBIOS, SMB
        },
        'iot': {
            'required': [],
            'common': [80, 1883, 8883],
            'indicators': [1883, 8883],  # MQTT
        },
    }
    
    def __init__(self, gateway_ip: Optional[str] = None):
        """
        Initialize device classifier.
        
        Args:
            gateway_ip: IP address of the network gateway
        """
        self.gateway_ip = gateway_ip
    
    def classify(
        self,
        ip: str,
        ports: List[int],
        mac_vendor: Optional[str],
        hostname: str,
        ttl: Optional[int] = None,
        is_gateway: bool = False
    ) -> DeviceClassification:
        """
        Classify device using multi-factor analysis.
        
        Args:
            ip: Device IP address
            ports: List of open ports
            mac_vendor: MAC address vendor
            hostname: Device hostname
            ttl: TTL value from ping (for OS detection)
            is_gateway: Whether this is the network gateway
        
        Returns:
            DeviceClassification with type, hierarchy, and confidence
        """
        reasoning = []
        confidence = 50  # Start with neutral confidence
        
        # Determine hierarchy first
        hierarchy = self._determine_hierarchy(
            ip, ports, mac_vendor, hostname, is_gateway, reasoning
        )
        
        # Determine device type
        device_type = self._determine_type(
            ports, mac_vendor, hostname, ttl, hierarchy, reasoning
        )
        
        # Calculate confidence based on evidence strength
        confidence = self._calculate_confidence(
            device_type, hierarchy, ports, mac_vendor, hostname, reasoning
        )
        
        method = self._determine_method(reasoning)
        
        return DeviceClassification(
            device_type=device_type,
            hierarchy=hierarchy,
            confidence=confidence,
            method=method,
            reasoning=reasoning
        )
    
    def _determine_hierarchy(
        self,
        ip: str,
        ports: List[int],
        mac_vendor: Optional[str],
        hostname: str,
        is_gateway: bool,
        reasoning: List[str]
    ) -> str:
        """Determine device hierarchy level"""
        
        # UTAMA: Main infrastructure
        if is_gateway or ip == self.gateway_ip:
            reasoning.append("Gateway device → utama")
            return 'utama'
        
        hostname_lower = hostname.lower()
        vendor_lower = (mac_vendor or '').lower()
        
        # Check for infrastructure keywords
        infra_keywords = ['router', 'gateway', 'firewall', 'core', 'main']
        if any(kw in hostname_lower for kw in infra_keywords):
            reasoning.append(f"Infrastructure keyword in hostname → utama")
            return 'utama'
        
        # SUB: Secondary infrastructure
        sub_keywords = ['switch', 'ap', 'access', 'server', 'nas', 'storage']
        if any(kw in hostname_lower for kw in sub_keywords):
            reasoning.append(f"Infrastructure keyword in hostname → sub")
            return 'sub'
        
        # Network equipment vendors
        network_vendors = ['cisco', 'mikrotik', 'ubiquiti', 'juniper', 'aruba', 'netgear']
        if any(nv in vendor_lower for nv in network_vendors):
            # If it has SNMP (161), likely sub infrastructure
            if 161 in ports:
                reasoning.append(f"Network vendor + SNMP → sub")
                return 'sub'
        
        # Port-based infrastructure detection
        if 161 in ports:  # SNMP
            reasoning.append("SNMP port → sub")
            return 'sub'
        
        if 23 in ports:  # Telnet (old network devices)
            reasoning.append("Telnet port → sub")
            return 'sub'
        
        # SSH + Web = likely server/infrastructure
        if 22 in ports and (80 in ports or 443 in ports):
            # But not if it's a desktop (has SMB)
            if not (135 in ports and 139 in ports and 445 in ports):
                reasoning.append("SSH + Web (no SMB) → sub")
                return 'sub'
        
        # DNS without SMB = likely router/DNS server
        if 53 in ports and not (139 in ports or 445 in ports):
            reasoning.append("DNS (no SMB) → sub")
            return 'sub'
        
        # NAS ports
        if any(p in ports for p in [548, 2049, 5000, 5001]):  # AFP, NFS, Synology
            reasoning.append("NAS ports → sub")
            return 'sub'
        
        # DEVICE: Endpoints
        # Windows PC signature
        if 135 in ports and 139 in ports and 445 in ports:
            reasoning.append("Windows SMB signature → device")
            return 'device'
        
        # Printer ports
        if any(p in ports for p in [9100, 631, 515]):
            reasoning.append("Printer ports → device")
            return 'device'
        
        # RDP/VNC
        if 3389 in ports or 5900 in ports:
            reasoning.append("Remote desktop → device")
            return 'device'
        
        # Camera ports
        if 554 in ports or 37777 in ports:
            reasoning.append("Camera ports → device")
            return 'device'
        
        # Default to device if uncertain
        reasoning.append("No strong indicators → device")
        return 'device'
    
    def _determine_type(
        self,
        ports: List[int],
        mac_vendor: Optional[str],
        hostname: str,
        ttl: Optional[int],
        hierarchy: str,
        reasoning: List[str]
    ) -> str:
        """Determine specific device type"""
        
        hostname_lower = hostname.lower()
        vendor_lower = (mac_vendor or '').lower()
        
        # Explicit hostname indicators (highest priority)
        type_keywords = {
            'router': ['router', 'gateway', 'gw'],
            'switch': ['switch', 'sw'],
            'access_point': ['ap', 'access-point', 'wifi', 'wireless'],
            'server': ['server', 'srv'],
            'nas': ['nas', 'storage'],
            'printer': ['printer', 'print'],
            'camera': ['camera', 'cam', 'ipcam'],
        }
        
        for device_type, keywords in type_keywords.items():
            if any(kw in hostname_lower for kw in keywords):
                reasoning.append(f"Hostname keyword → {device_type}")
                return device_type
        
        # Vendor-based detection
        if 'hikvision' in vendor_lower or 'dahua' in vendor_lower or 'axis' in vendor_lower:
            reasoning.append(f"Camera vendor → camera")
            return 'camera'
        
        if 'synology' in vendor_lower or 'qnap' in vendor_lower:
            reasoning.append(f"NAS vendor → nas")
            return 'nas'
        
        if 'raspberry pi' in vendor_lower:
            reasoning.append(f"Raspberry Pi → iot")
            return 'iot'
        
        # Port-based classification (check signatures)
        best_match = None
        best_score = 0
        
        for device_type, signature in self.PORT_SIGNATURES.items():
            score = 0
            matched_ports = []
            
            # Check indicator ports (strong signal)
            for port in signature['indicators']:
                if port in ports:
                    score += 10
                    matched_ports.append(port)
            
            # Check common ports (weak signal)
            common_matches = sum(1 for p in signature['common'] if p in ports)
            score += common_matches
            
            if score > best_score:
                best_score = score
                best_match = device_type
        
        if best_match and best_score >= 10:
            reasoning.append(f"Port signature match → {best_match}")
            return best_match
        
        # Hierarchy-based fallback
        if hierarchy == 'utama':
            reasoning.append("Hierarchy-based → router")
            return 'router'
        elif hierarchy == 'sub':
            # Most common sub infrastructure
            if 161 in ports:
                reasoning.append("Sub with SNMP → switch")
                return 'switch'
            reasoning.append("Sub infrastructure → server")
            return 'server'
        
        # Default endpoints
        reasoning.append("Default classification → other")
        return 'other'
    
    def _calculate_confidence(
        self,
        device_type: str,
        hierarchy: str,
        ports: List[int],
        mac_vendor: Optional[str],
        hostname: str,
        reasoning: List[str]
    ) -> int:
        """Calculate classification confidence (0-100)"""
        
        confidence = 50
        
        # Boost confidence for strong evidence
        evidence_count = len(reasoning)
        
        # Multiple pieces of evidence = higher confidence
        if evidence_count >= 4:
            confidence += 30
        elif evidence_count >= 3:
            confidence += 20
        elif evidence_count >= 2:
            confidence += 10
        
        # Vendor match boosts confidence
        if mac_vendor:
            confidence += 15
        
        # Hostname match boosts confidence
        hostname_lower = hostname.lower()
        type_in_hostname = device_type.replace('_', '') in hostname_lower.replace('-', '').replace('_', '')
        if type_in_hostname:
            confidence += 20
        
        # Gateway detection is highly confident
        if 'Gateway' in reasoning[0] if reasoning else False:
            confidence = 100
        
        # SNMP detection is quite reliable
        if 'SNMP' in str(reasoning):
            confidence += 10
        
        # Clamp to 0-100
        return max(0, min(100, confidence))
    
    def _determine_method(self, reasoning: List[str]) -> str:
        """Determine primary classification method used"""
        
        reasoning_str = ' '.join(reasoning).lower()
        
        if 'snmp' in reasoning_str:
            return 'snmp'
        elif 'vendor' in reasoning_str:
            return 'mac_vendor'
        elif 'hostname' in reasoning_str or 'keyword' in reasoning_str:
            return 'hostname'
        elif 'port' in reasoning_str or 'signature' in reasoning_str:
            return 'ports'
        elif 'gateway' in reasoning_str:
            return 'network_topology'
        else:
            return 'heuristic'
