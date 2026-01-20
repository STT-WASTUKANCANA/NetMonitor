"""
Cookie-based session persistence for Streamlit.
More reliable than localStorage for server-side session management.
"""
import streamlit.components.v1 as components
from typing import Optional, Dict, Any
import json
import base64


def set_cookie(name: str, value: str, days: int = 7) -> None:
    """
    Set a browser cookie.
    
    Args:
        name: Cookie name
        value: Cookie value
        days: Cookie expiry in days
    """
    # Encode value to base64 to handle special characters
    encoded_value = base64.b64encode(value.encode()).decode()
    
    html = f"""
    <script>
    function setCookie(name, value, days) {{
        let expires = "";
        if (days) {{
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }}
        document.cookie = name + "=" + (value || "") + expires + "; path=/; SameSite=Strict";
    }}
    
    setCookie('{name}', '{encoded_value}', {days});
    </script>
    """
    
    components.html(html, height=0)


def get_cookie(name: str) -> Optional[str]:
    """
    Get a browser cookie value.
    
    Args:
        name: Cookie name
        
    Returns:
        Cookie value or None
    """
    html = f"""
    <script>
    function getCookie(name) {{
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for(let i = 0; i < ca.length; i++) {{
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) {{
                return c.substring(nameEQ.length, c.length);
            }}
        }}
        return null;
    }}
    
    const cookieValue = getCookie('{name}');
    if (cookieValue) {{
        // Send to parent via URL hash (simple communication method)
        if (window.parent) {{
            window.parent.postMessage({{
                type: 'cookie_value',
                name: '{name}',
                value: cookieValue
            }}, '*');
        }}
    }}
    </script>
    """
    
    components.html(html, height=0)
    return None


def delete_cookie(name: str) -> None:
    """
    Delete a browser cookie.
    
    Args:
        name: Cookie name
    """
    html = f"""
    <script>
    function deleteCookie(name) {{
        document.cookie = name + '=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT; SameSite=Strict';
    }}
    
    deleteCookie('{name}');
    </script>
    """
    
    components.html(html, height=0)


def save_session_cookies(token: str, user: Dict, token_expiry: str, last_activity: str) -> None:
    """
    Save session data as cookies.
    
    Args:
        token: JWT token
        user: User data dict
        token_expiry: Token expiry timestamp (ISO format)
        last_activity: Last activity timestamp (ISO format)
    """
    # Save as separate cookies
    set_cookie('nm_token', token, days=7)
    set_cookie('nm_user', json.dumps(user), days=7)
    set_cookie('nm_token_expiry', token_expiry, days=7)
    set_cookie('nm_last_activity', last_activity, days=7)


def clear_session_cookies() -> None:
    """Clear all session cookies."""
    delete_cookie('nm_token')
    delete_cookie('nm_user')
    delete_cookie('nm_token_expiry')
    delete_cookie('nm_last_activity')
