"""
Session management utilities for Streamlit.
"""
import streamlit as st
from datetime import datetime, timedelta
from typing import Optional, Dict

from streamlit_app.utils.api_client import api_client
from streamlit_app.config import config


def init_session_state():
    """Initialize session state variables."""
    if "authenticated" not in st.session_state:
        st.session_state.authenticated = False
    if "user" not in st.session_state:
        st.session_state.user = None
    if "token" not in st.session_state:
        st.session_state.token = None
    if "token_expiry" not in st.session_state:
        st.session_state.token_expiry = None
    if "last_activity" not in st.session_state:
        st.session_state.last_activity = datetime.now()
    if "login_attempts" not in st.session_state:
        st.session_state.login_attempts = {}
    if "session_timeout_warning_shown" not in st.session_state:
        st.session_state.session_timeout_warning_shown = False
    if "session_created_at" not in st.session_state:
        st.session_state.session_created_at = None



def login(email: str, password: str) -> Dict:
    """
    Attempt to login user with brute force protection.
    Returns API response.
    """
    from streamlit_app.auth import check_login_attempts, record_login_attempt
    
    # Check if user is locked out
    allowed, message = check_login_attempts(
        email, 
        max_attempts=config.MAX_LOGIN_ATTEMPTS,
        lockout_minutes=config.LOCKOUT_DURATION_MINUTES
    )
    
    if not allowed:
        return {"success": False, "message": message}
    
    # Try API authentication first
    response = api_client.login(email, password)
    
    # Fallback to demo users if API fails
    
    if response.get("success"):
        data = response.get("data", {})
        st.session_state.authenticated = True
        st.session_state.token = data.get("token")
        st.session_state.user = data.get("user")
        st.session_state.token_expiry = datetime.now() + timedelta(hours=config.SESSION_EXPIRY_HOURS) 
        st.session_state.last_activity = datetime.now()
        st.session_state.session_created_at = datetime.now()
        st.session_state.session_timeout_warning_shown = False
        
        # Record successful login
        record_login_attempt(email, success=True)
        
        # Set token in API client
        api_client.set_token(st.session_state.token)
    else:
        # Record failed login
        record_login_attempt(email, success=False)
    
    return response



def logout():
    """Logout user and clear all session data."""
    api_client.logout()
    st.session_state.authenticated = False
    st.session_state.user = None
    st.session_state.token = None
    st.session_state.token_expiry = None
    st.session_state.last_activity = None
    st.session_state.session_created_at = None
    st.session_state.session_timeout_warning_shown = False
    api_client.set_token(None)


def is_authenticated() -> bool:
    """
    Check if user is authenticated with strict timeout enforcement.
    Implements 15-minute idle timeout with warnings.
    """
    if not st.session_state.authenticated:
        return False
    
    # Check session inactivity (strict 15-minute timeout)
    if st.session_state.last_activity:
        time_since_activity = datetime.now() - st.session_state.last_activity
        timeout_delta = timedelta(minutes=config.SESSION_TIMEOUT_MINUTES)
        
        # Session expired
        if time_since_activity > timeout_delta:
            st.toast("⏰ Session expired due to inactivity", icon="⚠️")
            st.warning("Your session has expired. Please login again.")
            logout()
            return False
        
        # Show warning when approaching timeout
        warning_delta = timedelta(minutes=config.SESSION_WARNING_MINUTES)
        if (timeout_delta - time_since_activity) < warning_delta:
            if not st.session_state.session_timeout_warning_shown:
                from streamlit_app.auth import get_session_time_remaining
                remaining = get_session_time_remaining(
                    st.session_state.last_activity,
                    config.SESSION_TIMEOUT_MINUTES
                )
                minutes_remaining = remaining // 60
                st.toast(f"⏰ Your session will expire in {minutes_remaining} minute(s)", icon="⚠️")
                st.session_state.session_timeout_warning_shown = True
        else:
            # Reset warning flag if user becomes active again
            st.session_state.session_timeout_warning_shown = False
        
        # Update activity timestamp (user is active by being on the page)
        st.session_state.last_activity = datetime.now()

    # Check token expiry (failsafe for backend token)
    if st.session_state.token_expiry:
        if datetime.now() > st.session_state.token_expiry:
            logout()
            return False
    
    # Set token in API client if not set
    if st.session_state.token and not api_client.token:
        api_client.set_token(st.session_state.token)
    
    return True


def get_current_user() -> Optional[Dict]:
    """Get current user from session."""
    return st.session_state.user if is_authenticated() else None


def is_admin() -> bool:
    """Check if current user is admin."""
    user = get_current_user()
    return user.get("role") == "admin" if user else False


def get_session_time_remaining() -> int:
    """
    Get remaining session time in seconds.
    
    Returns:
        Remaining seconds (0 if expired or not authenticated)
    """
    if not is_authenticated() or not st.session_state.last_activity:
        return 0
    
    from streamlit_app.auth import get_session_time_remaining as calc_remaining
    return calc_remaining(
        st.session_state.last_activity,
        config.SESSION_TIMEOUT_MINUTES
    )


def format_session_time() -> str:
    """
    Format remaining session time for display.
    
    Returns:
        Formatted time string
    """
    from streamlit_app.auth import format_time_remaining
    remaining = get_session_time_remaining()
    return format_time_remaining(remaining)


def update_activity():
    """Manually update last activity timestamp."""
    if st.session_state.authenticated:
        st.session_state.last_activity = datetime.now()


def require_auth():
    """
    Decorator/function to require authentication.
    Redirects to login if not authenticated.
    """
    init_session_state()
    
    if not is_authenticated():
        show_login_page()
        st.stop()


def show_login_page():
    """Display enhanced login page with premium design."""
    st.set_page_config(
        page_title=f"Login - {config.PAGE_TITLE}",
        page_icon=config.PAGE_ICON,
        layout="centered"
    )
    
    # Custom CSS for premium login page
    st.markdown("""
    <style>
    .login-container {
        max-width: 450px;
        margin: 2rem auto;
        padding: 2.5rem;
        background: #FFFFFF;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        border: 1px solid #E2E8F0;
    }
    .login-title {
        text-align: center;
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: #1A202C;
    }
    .login-subtitle {
        text-align: center;
        color: #64748B;
        margin-bottom: 2rem;
    }
    .stButton>button {
        background: linear-gradient(135deg, #667EEA 0%, #764BA2 100%);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .stButton>button:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
    }
    .demo-info {
        background: #F7FAFC;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1.5rem;
        font-size: 0.875rem;
        color: #4A5568;
    }
    </style>
    """, unsafe_allow_html=True)
    
    # Center the login form
    col1, col2, col3 = st.columns([1, 2, 1])
    
    with col2:
        # Logo and title
        st.markdown(f"""
        <div style="text-align: center; margin-bottom: 2rem;">
            <div style="font-size: 4rem; margin-bottom: 0.5rem;">{config.PAGE_ICON}</div>
            <h1 class="login-title">{config.PAGE_TITLE}</h1>
            <p class="login-subtitle">Network Monitoring System</p>
        </div>
        """, unsafe_allow_html=True)
        
        # Login form
        with st.form("login_form", clear_on_submit=False):
            st.markdown("### 🔐 Login to your account")
            
            email = st.text_input(
                "Email Address",
                placeholder="your-email@example.com",
                help="Enter your email address"
            )
            password = st.text_input(
                "Password",
                type="password",
                placeholder="Enter your password",
                help="Enter your password"
            )
            
            st.markdown("")  # Spacing
            submitted = st.form_submit_button("🚀 Login", use_container_width=True)
            
            if submitted:
                if not email or not password:
                    st.error("⚠️ Please enter both email and password")
                else:
                    with st.spinner("🔄 Authenticating..."):
                        response = login(email, password)
                        
                        if response.get("success"):
                            st.success("✅ Login successful! Redirecting...")
                            st.balloons()
                            st.rerun()
                        else:
                            error_msg = response.get("message", "Login failed")
                            st.error(f"❌ {error_msg}")
                            
                            # Show remaining attempts if locked out
                            if "try again" in error_msg.lower():
                                st.warning("⏳ Your account has been temporarily locked due to multiple failed login attempts.")
        
        # Footer
        st.markdown("""
        <div style="text-align: center; margin-top: 2rem; color: #94A3B8; font-size: 0.875rem;">
            Secure authentication powered by NetMonitor
        </div>
        """, unsafe_allow_html=True)
