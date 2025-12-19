"""
Reusable UI components for Streamlit.
"""
import streamlit as st
import plotly.express as px
import plotly.graph_objects as go
from typing import List, Dict, Optional
from datetime import datetime


def metric_card(title: str, value: str, subtitle: str = None, icon: str = None, color: str = None):
    """Display a responsive metric card with glassmorphism effect."""
    # Determine accent color for border
    accent_color = color if color and not color.startswith('#FFFFFF') else "var(--primary-500)"
    
    st.markdown(f"""
    <div class="metric-card" style="
        border-left: 4px solid {accent_color};
    ">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: var(--space-sm);">
            <div style="flex: 1; min-width: 120px;">
                <p style="color: var(--neutral-500); font-size: var(--text-sm); margin: 0; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 500;">{title}</p>
                <p style="color: var(--neutral-900); font-size: var(--text-3xl); font-weight: 800; margin: var(--space-xs) 0; line-height: 1.2;">{value}</p>
                {f'<p style="color: var(--neutral-600); font-size: var(--text-xs); margin: 0;">{subtitle}</p>' if subtitle else ''}
            </div>
            {f'<span style="font-size: var(--text-3xl); opacity: 0.8;">{icon}</span>' if icon else ''}
        </div>
    </div>
    """, unsafe_allow_html=True)


def status_badge(status: str, size: str = "normal") -> str:
    """Generate HTML for responsive status badge."""
    colors = {
        "up": ("var(--success-500)", "✅ UP"),
        "down": ("var(--danger-500)", "❌ DOWN"),
        "unknown": ("var(--neutral-500)", "❓ Unknown"),
        "active": ("var(--warning-500)", "🔔 Active"),
        "acknowledged": ("var(--primary-500)", "👁️ Acknowledged"),
        "resolved": ("var(--success-500)", "✅ Resolved"),
        "critical": ("var(--danger-500)", "🔴 Critical"),
        "high": ("#F97316", "🟠 High"),
        "medium": ("var(--warning-500)", "🟡 Medium"),
        "low": ("var(--success-500)", "🟢 Low")
    }
    color, label = colors.get(status.lower(), ("var(--neutral-500)", status))
    
    padding = "var(--space-xs) var(--space-sm)" if size == "normal" else "2px var(--space-xs)"
    font_size = "var(--text-sm)" if size == "normal" else "var(--text-xs)"
    
    return f"""
    <span style="
        background: {color}20;
        color: {color};
        padding: {padding};
        border-radius: var(--radius-full);
        font-size: {font_size};
        font-weight: 600;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    ">{label}</span>
    """


def create_response_time_chart(labels: List[str], data: List[float], full_labels: List[str] = None) -> go.Figure:
    """Create response time line chart."""
    fig = go.Figure()
    
    fig.add_trace(go.Scatter(
        x=labels,
        y=data,
        mode='lines+markers',
        name='Response Time',
        line=dict(color='#4F46E5', width=2),
        marker=dict(size=4),
        fill='tozeroy',
        fillcolor='rgba(79, 70, 229, 0.1)',
        hovertemplate='<b>%{text}</b><br>Response Time: %{y:.2f} sms<extra></extra>',
        text=full_labels if full_labels else labels
    ))
    
    fig.update_layout(
        title=None,
        xaxis_title=None,
        yaxis_title="Response Time (ms)",
        template="plotly_white",
        height=280,
        autosize=True,
        margin=dict(l=10, r=10, t=20, b=40),
        paper_bgcolor='rgba(0,0,0,0)',
        plot_bgcolor='rgba(0,0,0,0)',
        xaxis=dict(
            showgrid=False,
            tickfont=dict(color='#64748B', size=10),
            tickangle=-45
        ),
        yaxis=dict(
            gridcolor='#E2E8F0',
            tickfont=dict(color='#64748B', size=10)
        ),
        hovermode="x unified",
        font=dict(color='#1A1A1A', size=12)
    )
    
    return fig


def create_device_status_chart(labels: List[str], up_data: List[int], down_data: List[int], full_labels: List[str] = None) -> go.Figure:
    """Create device status stacked area chart."""
    fig = go.Figure()
    
    fig.add_trace(go.Scatter(
        x=labels,
        y=up_data,
        name='UP',
        fill='tozeroy',
        line=dict(color='#10B981', width=2),
        fillcolor='rgba(16, 185, 129, 0.3)',
        hovertemplate='<b>%{text}</b><br>UP: %{y}<extra></extra>',
        text=full_labels if full_labels else labels
    ))
    
    fig.add_trace(go.Scatter(
        x=labels,
        y=down_data,
        name='DOWN',
        fill='tozeroy',
        line=dict(color='#EF4444', width=2),
        fillcolor='rgba(239, 68, 68, 0.3)',
        hovertemplate='<b>%{text}</b><br>DOWN: %{y}<extra></extra>',
        text=full_labels if full_labels else labels
    ))
    
    fig.update_layout(
        title=None,
        xaxis_title=None,
        yaxis_title="Count",
        template="plotly_white",
        height=280,
        autosize=True,
        margin=dict(l=10, r=10, t=30, b=40),
        paper_bgcolor='rgba(0,0,0,0)',
        plot_bgcolor='rgba(0,0,0,0)',
        xaxis=dict(
            showgrid=False,
            tickfont=dict(color='#64748B', size=10),
            tickangle=-45
        ),
        yaxis=dict(
            gridcolor='#E2E8F0',
            tickfont=dict(color='#64748B', size=10)
        ),
        legend=dict(
            orientation="h",
            yanchor="bottom",
            y=1.02,
            xanchor="right",
            x=1,
            font=dict(color='#1A1A1A', size=11)
        ),
        hovermode="x unified",
        font=dict(color='#1A1A1A', size=12)
    )
    
    return fig


def create_uptime_gauge(percentage: float) -> go.Figure:
    """Create uptime gauge chart."""
    color = "#10B981" if percentage >= 95 else "#F59E0B" if percentage >= 90 else "#EF4444"
    
    fig = go.Figure(go.Indicator(
        mode="gauge+number",
        value=percentage,
        number={'suffix': '%', 'font': {'size': 24, 'color': '#1A1A1A'}},
        gauge={
            'axis': {'range': [0, 100], 'tickcolor': '#64748B'},
            'bar': {'color': color},
            'bgcolor': '#F1F5F9',
            'borderwidth': 0,
            'steps': [
                {'range': [0, 90], 'color': 'rgba(239, 68, 68, 0.125)'},
                {'range': [90, 95], 'color': 'rgba(245, 158, 11, 0.125)'},
                {'range': [95, 100], 'color': 'rgba(16, 185, 129, 0.125)'}
            ]
        }
    ))
    
    fig.update_layout(
        template="plotly_white",
        height=200,
        margin=dict(l=20, r=20, t=20, b=20),
        paper_bgcolor='rgba(0,0,0,0)',
        font=dict(color='#1A1A1A')
    )
    
    return fig


def device_tree_item(device: Dict, level: int = 0):
    """Render device tree item."""
    indent = "  " * level
    status_icon = "✅" if device.get("status") == "up" else "❌" if device.get("status") == "down" else "❓"
    type_icon = device.get("type_icon", "📡")
    
    st.markdown(f"""
    <div style="padding-left: {level * 20}px; margin: 0.25rem 0;">
        <span>{type_icon} {device.get('name')} ({device.get('ip_address')}) {status_icon}</span>
    </div>
    """, unsafe_allow_html=True)
    
    for child in device.get("children", []):
        device_tree_item(child, level + 1)


def alert_row(alert: Dict):
    """Render responsive alert row with glassmorphism."""
    device = alert.get("device", {})
    created_at = alert.get("created_at", "")
    
    if created_at:
        try:
            dt = datetime.fromisoformat(created_at.replace('Z', '+00:00'))
            time_ago = format_time_ago(dt)
        except:
            time_ago = created_at
    else:
        time_ago = "Unknown"
    
    severity_icons = {
        "critical": "🔴",
        "high": "🟠",
        "medium": "🟡",
        "low": "🟢"
    }
    
    severity_colors = {
        "critical": "var(--danger-500)",
        "high": "#F97316",
        "medium": "var(--warning-500)",
        "low": "var(--success-500)"
    }
    
    border_color = severity_colors.get(alert.get('severity'), 'var(--warning-500)')
    
    st.markdown(f"""
    <div class="alert-card" style="
        border-left: 4px solid {border_color};
    ">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: var(--space-xs);">
            <div style="display: flex; align-items: center; gap: var(--space-sm); flex-wrap: wrap;">
                <span style="font-size: var(--text-xl);">{severity_icons.get(alert.get('severity'), '🟡')}</span>
                <strong style="color: var(--neutral-900); font-size: var(--text-base);">{device.get('name', 'Unknown')}</strong>
            </div>
            <span style="color: var(--neutral-500); font-size: var(--text-xs); white-space: nowrap;">{time_ago}</span>
        </div>
        <p style="color: var(--neutral-600); margin: var(--space-xs) 0 0 var(--space-xl); font-size: var(--text-sm); line-height: 1.5;">{alert.get('message', '')}</p>
    </div>
    """, unsafe_allow_html=True)


def format_time_ago(dt: datetime) -> str:
    """Format datetime as relative time."""
    # Convert incoming datetime to Jakarta time if needed
    if dt.tzinfo is None:
        jakarta_tz = pytz.timezone('Asia/Jakarta')
        dt = jakarta_tz.localize(dt)
    else:
        dt = dt.astimezone(pytz.timezone('Asia/Jakarta'))

    # Also convert 'now' to Jakarta time
    jakarta_now = datetime.now(pytz.timezone('Asia/Jakarta'))
    diff = jakarta_now - dt

    if diff.days > 0:
        return f"{diff.days}d ago"
    elif diff.seconds >= 3600:
        return f"{diff.seconds // 3600}h ago"
    elif diff.seconds >= 60:
        return f"{diff.seconds // 60}m ago"
    else:
        return "Just now"
