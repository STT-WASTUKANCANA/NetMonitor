"""
Reusable UI components for Streamlit.
"""
import streamlit as st
import plotly.express as px
import plotly.graph_objects as go
from typing import List, Dict, Optional
from datetime import datetime


def metric_card(title: str, value: str, subtitle: str = None, icon: str = None, color: str = None):
    """Display a metric card."""
    bg_color = color if color and color.startswith('#FFFFFF') else "#FFFFFF"
    border_color = color if color and not color.startswith('#FFFFFF') else "#E2E8F0"
    
    st.markdown(f"""
    <div style="
        background-color: {bg_color};
        border-radius: 12px;
        padding: 1.5rem;
        border: 1px solid {border_color};
        height: 100%;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    ">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            <div>
                <p style="color: #64748B; font-size: 0.875rem; margin: 0;">{title}</p>
                <p style="color: #1A1A1A; font-size: 2rem; font-weight: bold; margin: 0.5rem 0;">{value}</p>
                {f'<p style="color: #475569; font-size: 0.75rem; margin: 0;">{subtitle}</p>' if subtitle else ''}
            </div>
            {f'<span style="font-size: 2rem;">{icon}</span>' if icon else ''}
        </div>
    </div>
    """, unsafe_allow_html=True)


def status_badge(status: str, size: str = "normal") -> str:
    """Generate HTML for status badge."""
    colors = {
        "up": ("#10B981", "✅ UP"),
        "down": ("#EF4444", "❌ DOWN"),
        "unknown": ("#6B7280", "❓ Unknown"),
        "active": ("#F59E0B", "🔔 Active"),
        "acknowledged": ("#3B82F6", "👁️ Acknowledged"),
        "resolved": ("#10B981", "✅ Resolved"),
        "critical": ("#EF4444", "🔴 Critical"),
        "high": ("#F97316", "🟠 High"),
        "medium": ("#F59E0B", "🟡 Medium"),
        "low": ("#10B981", "🟢 Low")
    }
    color, label = colors.get(status.lower(), ("#6B7280", status))
    
    padding = "0.25rem 0.75rem" if size == "normal" else "0.15rem 0.5rem"
    font_size = "0.875rem" if size == "normal" else "0.75rem"
    
    return f"""
    <span style="
        background: {color}20;
        color: {color};
        padding: {padding};
        border-radius: 9999px;
        font-size: {font_size};
        font-weight: 500;
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
        height=300,
        margin=dict(l=0, r=0, t=20, b=0),
        paper_bgcolor='rgba(0,0,0,0)',
        plot_bgcolor='rgba(0,0,0,0)',
        xaxis=dict(showgrid=False, tickfont=dict(color='#64748B')),
        yaxis=dict(gridcolor='#E2E8F0', tickfont=dict(color='#64748B')),
        hovermode="x unified",
        font=dict(color='#1A1A1A')
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
        height=300,
        margin=dict(l=0, r=0, t=20, b=0),
        paper_bgcolor='rgba(0,0,0,0)',
        plot_bgcolor='rgba(0,0,0,0)',
        xaxis=dict(showgrid=False, tickfont=dict(color='#64748B')),
        yaxis=dict(gridcolor='#E2E8F0', tickfont=dict(color='#64748B')),
        legend=dict(orientation="h", yanchor="bottom", y=1.02, xanchor="right", x=1, font=dict(color='#1A1A1A')),
        hovermode="x unified",
        font=dict(color='#1A1A1A')
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
    """Render alert row."""
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
    
    st.markdown(f"""
    <div style="
        background: #F1F5F9;
        border-radius: 8px;
        padding: 1rem;
        margin: 0.5rem 0;
        border-left: 4px solid {'#EF4444' if alert.get('severity') == 'critical' else '#F97316' if alert.get('severity') == 'high' else '#F59E0B'};
        border-top: 1px solid #E2E8F0;
        border-right: 1px solid #E2E8F0;
        border-bottom: 1px solid #E2E8F0;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    ">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <span style="font-size: 1.25rem;">{severity_icons.get(alert.get('severity'), '🟡')}</span>
                <strong style="color: #1A1A1A; margin-left: 0.5rem;">{device.get('name', 'Unknown')}</strong>
            </div>
            <span style="color: #64748B; font-size: 0.875rem;">{time_ago}</span>
        </div>
        <p style="color: #475569; margin: 0.5rem 0 0 2rem;">{alert.get('message', '')}</p>
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
