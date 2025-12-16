#!/usr/bin/env python3
"""
Network Statistics Module
Provides statistical analysis for network monitoring data
"""

import statistics
from typing import List, Optional, Tuple


class NetworkStatistics:
    """Statistical analysis utilities for network monitoring"""
    
    @staticmethod
    def calculate_baseline(history: List[float], method: str = 'median') -> Optional[float]:
        """
        Calculate baseline response time from history.
        
        Args:
            history: List of response time values
            method: 'mean', 'median', or 'trimmed_mean'
        
        Returns:
            Baseline value or None if insufficient data
        """
        if not history:
            return None
        
        # Filter out None values
        valid_values = [v for v in history if v is not None]
        
        if len(valid_values) < 3:
            return None
        
        if method == 'mean':
            return statistics.mean(valid_values)
        elif method == 'median':
            return statistics.median(valid_values)
        elif method == 'trimmed_mean':
            # Remove top and bottom 10% for trimmed mean
            sorted_values = sorted(valid_values)
            trim_count = max(1, len(sorted_values) // 10)
            trimmed = sorted_values[trim_count:-trim_count] if len(sorted_values) > 2 * trim_count else sorted_values
            return statistics.mean(trimmed) if trimmed else None
        else:
            return statistics.median(valid_values)
    
    @staticmethod
    def calculate_std_dev(history: List[float]) -> Optional[float]:
        """
        Calculate standard deviation of response times.
        
        Args:
            history: List of response time values
        
        Returns:
            Standard deviation or None if insufficient data
        """
        if not history:
            return None
        
        valid_values = [v for v in history if v is not None]
        
        if len(valid_values) < 2:
            return None
        
        try:
            return statistics.stdev(valid_values)
        except statistics.StatisticsError:
            return None
    
    @staticmethod
    def is_anomaly(
        value: float,
        baseline: Optional[float],
        std_dev: Optional[float],
        threshold: float = 2.0
    ) -> Tuple[bool, Optional[float]]:
        """
        Detect if value is an anomaly using z-score analysis.
        
        Args:
            value: Current response time
            baseline: Baseline response time
            std_dev: Standard deviation
            threshold: Number of standard deviations (default: 2.0)
        
        Returns:
            Tuple of (is_anomaly, z_score)
        """
        if baseline is None or std_dev is None or std_dev == 0:
            return False, None
        
        z_score = abs(value - baseline) / std_dev
        is_anomaly = z_score > threshold
        
        return is_anomaly, round(z_score, 2)
    
    @staticmethod
    def calculate_jitter(times: List[float]) -> Optional[float]:
        """
        Calculate network jitter (variance in response time).
        
        Args:
            times: List of consecutive response times
        
        Returns:
            Jitter value (average of absolute differences) or None
        """
        if not times or len(times) < 2:
            return None
        
        valid_times = [t for t in times if t is not None]
        
        if len(valid_times) < 2:
            return None
        
        # Calculate absolute differences between consecutive measurements
        differences = [abs(valid_times[i+1] - valid_times[i]) for i in range(len(valid_times) - 1)]
        
        return round(statistics.mean(differences), 2) if differences else None
    
    @staticmethod
    def calculate_percentiles(
        history: List[float]
    ) -> Optional[dict]:
        """
        Calculate percentile statistics (min, 25th, 50th, 75th, max, 95th, 99th).
        
        Args:
            history: List of response time values
        
        Returns:
            Dictionary with percentile values or None
        """
        if not history:
            return None
        
        valid_values = [v for v in history if v is not None]
        
        if len(valid_values) < 3:
            return None
        
        sorted_values = sorted(valid_values)
        
        return {
            'min': round(min(valid_values), 2),
            'p25': round(statistics.quantiles(sorted_values, n=4)[0], 2),
            'p50': round(statistics.median(valid_values), 2),
            'p75': round(statistics.quantiles(sorted_values, n=4)[2], 2),
            'p95': round(statistics.quantiles(sorted_values, n=20)[18], 2) if len(valid_values) >= 20 else round(max(valid_values), 2),
            'p99': round(statistics.quantiles(sorted_values, n=100)[98], 2) if len(valid_values) >= 100 else round(max(valid_values), 2),
            'max': round(max(valid_values), 2)
        }
    
    @staticmethod
    def calculate_reliability_score(
        consecutive_successes: int,
        consecutive_failures: int,
        packet_loss_avg: float,
        jitter: Optional[float],
        max_jitter_threshold: float = 50.0
    ) -> float:
        """
        Calculate overall reliability score (0-100).
        
        Args:
            consecutive_successes: Number of consecutive successful checks
            consecutive_failures: Number of consecutive failed checks
            packet_loss_avg: Average packet loss percentage
            jitter: Network jitter value
            max_jitter_threshold: Max acceptable jitter (default: 50ms)
        
        Returns:
            Reliability score (0-100)
        """
        score = 100.0
        
        # Penalize for consecutive failures
        if consecutive_failures > 0:
            score -= min(50, consecutive_failures * 10)
        
        # Reward for consecutive successes (up to 10)
        score += min(10, consecutive_successes)
        
        # Penalize for packet loss
        score -= packet_loss_avg
        
        # Penalize for high jitter
        if jitter is not None and jitter > max_jitter_threshold:
            jitter_penalty = min(20, (jitter - max_jitter_threshold) / 5)
            score -= jitter_penalty
        
        # Clamp to 0-100
        return max(0.0, min(100.0, round(score, 1)))
    
    @staticmethod
    def smooth_response_time(
        current: float,
        history: List[float],
        alpha: float = 0.3
    ) -> float:
        """
        Apply exponential smoothing to response time.
        
        Args:
            current: Current response time
            history: Historical response times
            alpha: Smoothing factor (0-1, higher = more weight to current)
        
        Returns:
            Smoothed response time
        """
        if not history:
            return current
        
        valid_history = [v for v in history if v is not None]
        
        if not valid_history:
            return current
        
        prev_avg = statistics.mean(valid_history[-5:])  # Use last 5 values
        smoothed = alpha * current + (1 - alpha) * prev_avg
        
        return round(smoothed, 2)
