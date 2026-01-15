"""
Streamlit App utilities package.
"""
from streamlit_app.utils.api_client import api_client, APIClient
from streamlit_app.utils.session import (
    init_session_state,
    login,
    logout,
    is_authenticated,
    get_current_user,
    is_admin,
    require_auth,
    get_session_time_remaining,
    format_session_time,
    update_activity,
    show_login_page
)

__all__ = [
    "api_client", "APIClient",
    "init_session_state", "login", "logout", "is_authenticated",
    "get_current_user", "is_admin", "require_auth",
    "get_session_time_remaining", "format_session_time", "update_activity",
    "show_login_page"
]
