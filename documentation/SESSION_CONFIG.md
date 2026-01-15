# Session Configuration Guide

## Session Persistence

Your NetMonitor app now has **persistent sessions** that keep you logged in across page refreshes!

## How It Works

1. **Login once** - Your session is saved in browser localStorage
2. **Refresh freely** - Session persists across page refreshes in the same browser tab
3. **24-hour validity** - Session expires after 24 hours or when you click Logout
4. **No inactivity timeout** - By default, you won't be logged out due to inactivity

## Configuration Options

Edit `streamlit_app/config.py` to customize session behavior:

```python
# Session Configuration
SESSION_EXPIRY_HOURS = 24           # JWT token expiry (24 hours)
SESSION_TIMEOUT_MINUTES = 120       # Inactivity timeout duration (2 hours)
SESSION_WARNING_MINUTES = 10        # Warning before timeout
ENABLE_SESSION_TIMEOUT = False      # Disable inactivity timeout (recommended)
```

### Enable Inactivity Timeout

If you want users to be logged out after inactivity:

```python
ENABLE_SESSION_TIMEOUT = True       # Enable inactivity timeout
SESSION_TIMEOUT_MINUTES = 30        # Logout after 30 minutes of inactivity
```

### Adjust Token Expiry

To change how long sessions last:

```python
SESSION_EXPIRY_HOURS = 48           # Sessions last 48 hours instead of 24
```

## Session Behavior

- ✅ **Persists across refresh** - Refresh the page without losing your session
- ✅ **Persists in same tab** - Keep working in the same browser tab
- ⚠️ **Clears on logout** - Clicking "Logout" clears the session
- ⚠️ **Expires after 24h** - Backend JWT token expires after configured hours
- ❌ **Not shared across tabs** - Each browser tab has its own session (Streamlit limitation)

## Troubleshooting

### Still being logged out on refresh?

1. **Check browser console** - Press F12 and look for JavaScript errors
2. **Check localStorage** - In console, run: `localStorage.getItem('netmonitor_token')`
3. **Clear cache** - Clear browser cache and cookies, then login again
4. **Check token expiry** - Token might be expired after 24 hours

### Want to completely disable auto-logout?

Set in `config.py`:
```python
ENABLE_SESSION_TIMEOUT = False      # No inactivity timeout
SESSION_EXPIRY_HOURS = 720          # 30 days token expiry
```

### Security Considerations

- Longer sessions = more convenient but less secure
- Shorter sessions = more secure but less convenient
- Recommended: Keep 24-hour expiry with timeout disabled for internal use
- For production: Enable timeout and use shorter expiry (e.g., 8 hours)
