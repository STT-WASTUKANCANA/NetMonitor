
import streamlit as st
from streamlit_app.config import config
import streamlit.components.v1 as components

def get_ws_url():
    """Convert API URL to WebSocket URL."""
    api_url = config.API_BASE_URL
    # Remove trailing slash if present
    if api_url.endswith('/'):
        api_url = api_url[:-1]
        
    if api_url.startswith("https"):
        return api_url.replace("https://", "wss://") + "/ws/alerts"
    else:
        return api_url.replace("http://", "ws://") + "/ws/alerts"

def inject_websocket_listener():
    """Inject WebSocket client JavaScript."""
    ws_url = get_ws_url()
    
    js_code = f"""
    <script>
    (function() {{
        // Unique ID to prevent multiple injections if cached
        if (window.netmonitor_ws_injected) return;
        window.netmonitor_ws_injected = true;
        
        const wsUrl = "{ws_url}";
        console.log("Connecting to NetMonitor WS:", wsUrl);
        
        let ws;
        let reconnectInterval = 5000;
        
        function connect() {{
            ws = new WebSocket(wsUrl);
            
            ws.onopen = function() {{
                console.log("✅ Connected to Alert WebSocket");
            }};
            
            ws.onmessage = function(event) {{
                console.log("📩 WS Message:", event.data);
                try {{
                    const msg = JSON.parse(event.data);
                    
                    if (msg.type === "device_status_update") {{
                        if (msg.data.status === "down") {{
                            showToast(`🚨 ALERT: ${{msg.data.device_name}} is DOWN!`, "error");
                        }} else if (msg.data.status === "up" && msg.data.old_status === "down") {{
                            showToast(`✅ RECOVERED: ${{msg.data.device_name}} is UP!`, "success");
                        }}
                    }}
                }} catch (e) {{
                    console.error("Error parsing WS message:", e);
                }}
            }};
            
            ws.onclose = function() {{
                console.log("⚠️ WS Closed. Reconnecting in " + reconnectInterval + "ms...");
                setTimeout(connect, reconnectInterval);
            }};
            
            ws.onerror = function(err) {{
                console.error("WS Error:", err);
                ws.close();
            }};
        }}
        
        function showToast(text, type) {{
            // Create toast container if not exists
            let container = document.getElementById("toast-container");
            if (!container) {{
                container = document.createElement("div");
                container.id = "toast-container";
                container.style.position = "fixed";
                container.style.bottom = "20px";
                container.style.right = "20px";
                container.style.zIndex = "999999";
                container.style.display = "flex";
                container.style.flexDirection = "column";
                container.style.gap = "10px";
                document.body.appendChild(container);
            }}
            
            let toast = document.createElement("div");
            toast.style.padding = "16px";
            toast.style.borderRadius = "8px";
            toast.style.color = "white";
            toast.style.fontFamily = "system-ui, -apple-system, sans-serif";
            toast.style.boxShadow = "0 4px 6px rgba(0,0,0,0.1)";
            toast.style.minWidth = "300px";
            toast.style.animation = "slideIn 0.3s ease-out";
            toast.style.opacity = "0";
            toast.style.transition = "opacity 0.3s";
            
            if (type === "error") {{
                toast.style.backgroundColor = "#EF4444"; // Red
                toast.style.borderLeft = "4px solid #991B1B";
            }} else if (type === "success") {{
                toast.style.backgroundColor = "#10B981"; // Green
                toast.style.borderLeft = "4px solid #065F46";
            }} else {{
                toast.style.backgroundColor = "#3B82F6"; // Blue
            }}
            
            toast.innerText = text;
            
            container.appendChild(toast);
            
            // Fade in
            requestAnimationFrame(() => {{
                toast.style.opacity = "1";
            }});
            
            // Auto remove
            setTimeout(() => {{
                toast.style.opacity = "0";
                setTimeout(() => container.removeChild(toast), 300);
            }}, 5000);
        }}
        
        // Add styles for animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {{
                from {{ transform: translateX(100%); opacity: 0; }}
                to {{ transform: translateX(0); opacity: 1; }}
            }}
        `;
        document.head.appendChild(style);
        
        connect();
    }})();
    </script>
    """
    
    # Inject into the parent window context if possible, but components.html is sandboxed iframe.
    # The Toast will appear INSIDE the iframe (which usually takes full width/height in some modes, or small box).
    # If height=0, it might be invisible.
    # TRICK: Streamlit components are iframes. To show a toast over the app, we need to break out or be visible.
    # Actually, if we set height=0, the toast inside the iframe with position:fixed will be cut off.
    # We must ensure the component has some size or use a different trick?
    # NO: components.html creates an iframe. Creating a fixed div inside that iframe puts it relative to the iframe.
    # If the iframe is hidden (height=0), the toast is hidden.
    # Logic fix: We simply cannot easily show a "Global Toast" from a Streamlit custom component (iframe) that covers the parent page without complex hacks (postMessage to parent, but parent needs listener).
    #
    # ALTERNATIVE: Just assume the user looks at the dashboard part where this component is placed.
    # OR: Place the component at the TOP or BOTTOM with sufficient height?
    #
    # BETTER: Use `st.toast`? But `st.toast` requires Python execution.
    # To trigger `st.toast` from WebSocket, we need the Python script to receive the message.
    # This loop requires `st_autorefresh` or `streamlit-websocket`.
    #
    # Since I cannot easily add packages and `requirements.txt` is fixed-ish (User said "Use npx... but for web app", here it's Streamlit).
    #
    # Let's try to put the component at the bottom of the sidebar or main page with sufficient height?
    # No, that's ugly.
    #
    # Let's go with the Python polling using `time.sleep` in a loop? No, that blocks.
    #
    # WAIT: User rules say "The user has 1 active workspaces...". I can edit `requirements.txt`.
    # I can add `streamlit-autorefresh`.
    # `streamlit-autorefresh` allows generic polling.
    #
    # But the user asked for "WebSocket or SSE".
    #
    # Let's stick to the implementation plan: "Implement WebSocket... integrate in Streamlit... using streamlit-ws or polling optimization".
    # Plan decision: "We will implement a specialized polling mechanism... OR use a custom component `streamlit-websocket-client`."
    #
    # I'll try the JavaScript injection but I'll make the component visible but small?
    # Or, I can try to use `window.parent.document` access? Only works if same origin. Usually Streamlit host and iframe are same origin (localhost).
    # Let's try accessing `window.parent.document.body`.
    
    js_code_parent_access = js_code.replace("document.body.appendChild", "window.parent.document.body.appendChild")
    
    components.html(js_code_parent_access, height=0, width=0)
