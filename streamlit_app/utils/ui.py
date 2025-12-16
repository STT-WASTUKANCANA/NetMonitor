"""
UI Utilities for Streamlit App.
"""
import streamlit as st
from streamlit_app.config import config

def load_custom_css():
    """Inject custom CSS for styling."""
    st.markdown("""
    <style>
    /* Light theme base */
    .stApp {
        background-color: #F8FAFC;
    }
    
    /* Main container styling */
    .main .block-container {
        max-width: 100%;
        padding-top: 2rem;
        padding-bottom: 2rem;
    }
    
    /* Card styling */
    .metric-card {
        background-color: #FFFFFF;
        border-radius: 12px;
        padding: 1.5rem;
        border: 1px solid #E2E8F0;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }
    
    /* Sidebar styling */
    [data-testid="stSidebar"] {
        background-color: #F8FAFC;
        border-right: 1px solid #E2E8F0;
    }
    
    /* Hide Streamlit branding */
    #MainMenu {visibility: hidden;}
    footer {visibility: hidden;}
    
    /* Button styling */
    .stButton > button, 
    .stDownloadButton > button,
    .stFormSubmitButton > button {
        background: linear-gradient(135deg, #4F46E5 0%, #6366F1 100%);
        color: white !important;
        border: none;
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-weight: 500;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }
    
    .stButton > button *,
    .stDownloadButton > button *,
    .stFormSubmitButton > button * {
        color: white !important;
    }
    
    .stButton > button:hover,
    .stDownloadButton > button:hover,
    .stFormSubmitButton > button:hover {
        background: linear-gradient(135deg, #4338CA 0%, #4F46E5 100%);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    
    /* Tab styling */
    .stTabs [data-baseweb="tab-list"] {
        gap: 2px;
        background-color: #F1F5F9;
        border-radius: 8px;
        padding: 4px;
    }
    
    .stTabs [data-baseweb="tab"] {
        background-color: transparent;
        color: #64748B;
        border-radius: 6px;
    }
    
    .stTabs [aria-selected="true"] {
        background-color: #FFFFFF;
        color: #4F46E5;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }
    
    /* Alert styling */
    .stAlert {
        border-radius: 8px;
        border: 1px solid #E2E8F0;
    }
    
    /* Table styling */
    .stDataFrame {
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #E2E8F0;
    }
    
    /* Text colors */
    h1, h2, h3, h4, h5, h6 {
        color: #1A1A1A !important;
    }
    
    p, label, span, div {
        color: #1A1A1A;
    }

    .stMetricValue {
        color: #1A1A1A !important;
    }
    
    .stMetricLabel {
        color: #64748B !important;
    }
    
    /* JSON code block text color fix for light theme */
    code {
        color: #D946EF;
    }
    
    /* Fix for sidebar text alignment/color if needed */
    [data-testid="stSidebar"] .stMarkdown p {
        color: #1E293B;
    }
    </style>
    """, unsafe_allow_html=True)

def setup_page_config(title=None, icon=None, layout=None):
    """Setup page configuration and load CSS."""
    st.set_page_config(
        page_title=title or config.PAGE_TITLE,
        page_icon=icon or config.PAGE_ICON,
        layout=layout or config.LAYOUT,
        initial_sidebar_state="expanded"
    )
    load_custom_css()
