"""
UI Utilities for Streamlit App.
Comprehensive responsive CSS framework with premium design.
"""
import streamlit as st
from streamlit_app.config import config

def load_custom_css():
    """Inject comprehensive responsive CSS for premium, elegant design."""
    st.markdown("""
    <style>
    /* =========================================
       CSS VARIABLES - DESIGN TOKENS
       ========================================= */
    :root {
        /* === Breakpoints (for reference) === */
        /* Mobile: max-width 480px */
        /* Tablet: 481px - 768px */
        /* Desktop: 769px - 1440px */
        /* Large/TV: 1441px+ */
        
        /* === Typography Scale (Fluid) === */
        --text-xs: clamp(0.625rem, 1.5vw, 0.75rem);
        --text-sm: clamp(0.75rem, 2vw, 0.875rem);
        --text-base: clamp(0.875rem, 2.5vw, 1rem);
        --text-lg: clamp(1rem, 3vw, 1.125rem);
        --text-xl: clamp(1.125rem, 3.5vw, 1.25rem);
        --text-2xl: clamp(1.25rem, 4vw, 1.5rem);
        --text-3xl: clamp(1.5rem, 5vw, 1.875rem);
        --text-4xl: clamp(1.875rem, 6vw, 2.25rem);
        
        /* === Spacing Scale (Fluid) === */
        --space-xs: clamp(0.25rem, 1vw, 0.5rem);
        --space-sm: clamp(0.5rem, 1.5vw, 0.75rem);
        --space-md: clamp(0.75rem, 2vw, 1rem);
        --space-lg: clamp(1rem, 2.5vw, 1.5rem);
        --space-xl: clamp(1.5rem, 3vw, 2rem);
        --space-2xl: clamp(2rem, 4vw, 3rem);
        
        /* === Premium Colors === */
        --primary-50: #EEF2FF;
        --primary-100: #E0E7FF;
        --primary-200: #C7D2FE;
        --primary-500: #6366F1;
        --primary-600: #4F46E5;
        --primary-700: #4338CA;
        --primary-800: #3730A3;
        
        --success-50: #ECFDF5;
        --success-500: #10B981;
        --success-600: #059669;
        
        --warning-50: #FFFBEB;
        --warning-500: #F59E0B;
        --warning-600: #D97706;
        
        --danger-50: #FEF2F2;
        --danger-500: #EF4444;
        --danger-600: #DC2626;
        
        --neutral-50: #F8FAFC;
        --neutral-100: #F1F5F9;
        --neutral-200: #E2E8F0;
        --neutral-300: #CBD5E1;
        --neutral-400: #94A3B8;
        --neutral-500: #64748B;
        --neutral-600: #475569;
        --neutral-700: #334155;
        --neutral-800: #1E293B;
        --neutral-900: #0F172A;
        
        /* === Gradients === */
        --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --gradient-success: linear-gradient(135deg, #10B981 0%, #059669 100%);
        --gradient-premium: linear-gradient(135deg, #4F46E5 0%, #7C3AED 50%, #EC4899 100%);
        --gradient-glass: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(255,255,255,0.7) 100%);
        
        /* === Glassmorphism === */
        --glass-bg: rgba(255, 255, 255, 0.85);
        --glass-bg-strong: rgba(255, 255, 255, 0.95);
        --glass-border: rgba(255, 255, 255, 0.3);
        --glass-shadow: 0 8px 32px rgba(31, 38, 135, 0.12);
        
        /* === Shadows === */
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        --shadow-premium: 0 25px 50px -12px rgba(79, 70, 229, 0.25);
        
        /* === Border Radius === */
        --radius-sm: 6px;
        --radius-md: 8px;
        --radius-lg: 12px;
        --radius-xl: 16px;
        --radius-2xl: 24px;
        --radius-full: 9999px;
        
        /* === Transitions === */
        --transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
        --transition-normal: 300ms cubic-bezier(0.4, 0, 0.2, 1);
        --transition-slow: 500ms cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* =========================================
       BASE STYLES
       ========================================= */
    
    /* Light theme base with subtle pattern */
    .stApp {
        background: linear-gradient(180deg, #F8FAFC 0%, #EEF2FF 100%);
        background-attachment: fixed;
        min-height: 100vh;
    }
    
    /* Main container - responsive padding */
    .main .block-container {
        max-width: 100%;
        padding: var(--space-lg) var(--space-md);
    }
    
    /* Hide Streamlit branding */
    #MainMenu {visibility: hidden;}
    footer {visibility: hidden;}
    header[data-testid="stHeader"] {
        background: transparent;
    }
    
    /* =========================================
       TYPOGRAPHY - RESPONSIVE
       ========================================= */
    
    h1 {
        font-size: var(--text-4xl) !important;
        font-weight: 800 !important;
        color: var(--neutral-900) !important;
        letter-spacing: -0.025em;
        line-height: 1.2 !important;
    }
    
    h2 {
        font-size: var(--text-2xl) !important;
        font-weight: 700 !important;
        color: var(--neutral-800) !important;
        letter-spacing: -0.02em;
    }
    
    h3 {
        font-size: var(--text-xl) !important;
        font-weight: 600 !important;
        color: var(--neutral-800) !important;
    }
    
    h4, h5, h6 {
        font-weight: 600 !important;
        color: var(--neutral-700) !important;
    }
    
    p, label, span, div {
        color: var(--neutral-700);
        font-size: var(--text-base);
        line-height: 1.6;
    }
    
    /* =========================================
       SIDEBAR - RESPONSIVE & PREMIUM
       ========================================= */
    
    [data-testid="stSidebar"] {
        background: var(--glass-bg-strong);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border-right: 1px solid var(--neutral-200);
        box-shadow: var(--shadow-lg);
    }
    
    [data-testid="stSidebar"] > div:first-child {
        padding: var(--space-lg) var(--space-md);
    }
    
    [data-testid="stSidebar"] .stMarkdown p {
        color: var(--neutral-700);
        font-size: var(--text-sm);
    }
    
    /* Sidebar user card */
    .sidebar-user-card {
        background: var(--gradient-glass);
        border-radius: var(--radius-lg);
        padding: var(--space-md);
        margin-bottom: var(--space-md);
        border: 1px solid var(--neutral-200);
        box-shadow: var(--shadow-sm);
        transition: all var(--transition-normal);
    }
    
    .sidebar-user-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }
    
    /* =========================================
       CARDS - GLASSMORPHISM & RESPONSIVE
       ========================================= */
    
    .metric-card, .premium-card {
        background: var(--glass-bg);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-radius: var(--radius-xl);
        padding: var(--space-lg);
        border: 1px solid var(--neutral-200);
        box-shadow: var(--shadow-md);
        transition: all var(--transition-normal);
        position: relative;
        overflow: hidden;
    }
    
    .metric-card::before, .premium-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--gradient-primary);
        opacity: 0;
        transition: opacity var(--transition-normal);
    }
    
    .metric-card:hover, .premium-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-xl);
    }
    
    .metric-card:hover::before, .premium-card:hover::before {
        opacity: 1;
    }
    
    /* Alert Card Styling */
    .alert-card {
        background: var(--glass-bg);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border-radius: var(--radius-lg);
        padding: var(--space-md);
        margin: var(--space-sm) 0;
        border: 1px solid var(--neutral-200);
        box-shadow: var(--shadow-sm);
        transition: all var(--transition-normal);
    }
    
    .alert-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateX(4px);
    }
    
    /* =========================================
       BUTTONS - PREMIUM STYLING
       ========================================= */
    
    .stButton > button, 
    .stDownloadButton > button,
    .stFormSubmitButton > button {
        background: var(--gradient-primary) !important;
        color: white !important;
        border: none !important;
        border-radius: var(--radius-lg) !important;
        padding: var(--space-sm) var(--space-lg) !important;
        font-weight: 600 !important;
        font-size: var(--text-sm) !important;
        box-shadow: var(--shadow-md) !important;
        transition: all var(--transition-normal) !important;
        min-height: 44px; /* Touch-friendly */
        position: relative;
        overflow: hidden;
    }
    
    .stButton > button::before,
    .stDownloadButton > button::before,
    .stFormSubmitButton > button::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left var(--transition-slow);
    }
    
    .stButton > button:hover::before,
    .stDownloadButton > button:hover::before,
    .stFormSubmitButton > button:hover::before {
        left: 100%;
    }
    
    .stButton > button *,
    .stDownloadButton > button *,
    .stFormSubmitButton > button * {
        color: white !important;
    }
    
    .stButton > button:hover,
    .stDownloadButton > button:hover,
    .stFormSubmitButton > button:hover {
        transform: translateY(-2px) !important;
        box-shadow: var(--shadow-premium) !important;
    }
    
    .stButton > button:active,
    .stDownloadButton > button:active,
    .stFormSubmitButton > button:active {
        transform: translateY(0) !important;
    }
    
    /* =========================================
       TABS - MODERN STYLING
       ========================================= */
    
    .stTabs [data-baseweb="tab-list"] {
        gap: 4px;
        background-color: var(--neutral-100);
        border-radius: var(--radius-lg);
        padding: 6px;
        overflow-x: auto;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }
    
    .stTabs [data-baseweb="tab-list"]::-webkit-scrollbar {
        display: none;
    }
    
    .stTabs [data-baseweb="tab"] {
        background-color: transparent;
        color: var(--neutral-500);
        border-radius: var(--radius-md);
        font-weight: 500;
        padding: var(--space-sm) var(--space-md);
        transition: all var(--transition-normal);
        white-space: nowrap;
        min-height: 44px;
    }
    
    .stTabs [aria-selected="true"] {
        background-color: white;
        color: var(--primary-600);
        box-shadow: var(--shadow-md);
    }
    
    /* =========================================
       FORMS & INPUTS - RESPONSIVE
       ========================================= */
    
    .stTextInput > div > div > input,
    .stTextArea > div > div > textarea,
    .stNumberInput > div > div > input,
    .stSelectbox > div > div,
    .stMultiselect > div > div {
        border-radius: var(--radius-md) !important;
        border: 1px solid #E2E8F0 !important;
        /* Increased padding and height for better vertical alignment */
        padding: 8px 12px !important; 
        font-size: clamp(16px, 1.2rem, 18px) !important; /* Larger text */
        line-height: 1.5 !important;
        transition: all var(--transition-fast) !important;
        min-height: 48px !important; /* Taller touch target */
        background: white !important;
        display: flex !important;
        align-items: center !important; /* Vertical center */
    }

    /* Input focus states */
    .stTextInput > div > div > input:focus,
    .stTextArea > div > div > textarea:focus,
    .stNumberInput > div > div > input:focus {
        border-color: var(--primary-500) !important;
        box-shadow: 0 0 0 3px var(--primary-100) !important;
    }
    
    /* =========================================
       SELECTBOX - COMPREHENSIVE STYLING
       Fix: Text must be visible and black
       ========================================= */
    
    /* === Force all selectbox text to be visible, black, and large enough === */
    .stSelectbox,
    .stSelectbox * {
        color: #1a1a1a !important;
        font-size: clamp(16px, 1.2rem, 18px) !important;
    }
    
    /* === Selectbox Container === */
    .stSelectbox > div > div {
        background-color: #ffffff !important;
        color: #1a1a1a !important;
         /* Ensure flex alignment for the text inside */
        display: flex !important;
        align-items: center !important;
    }
    
    /* === The actual select control === */
    .stSelectbox [data-baseweb="select"] {
        background-color: #ffffff !important;
    }
    
    .stSelectbox [data-baseweb="select"] > div {
        color: #1a1a1a !important;
        background-color: #ffffff !important;
    }
    
    .stSelectbox [data-baseweb="select"] > div > div {
        color: #1a1a1a !important;
    }
    
    .stSelectbox [data-baseweb="select"] span {
        color: #1a1a1a !important;
    }
    
    .stSelectbox [data-baseweb="select"] div {
        color: #1a1a1a !important;
    }
    
    /* === Ensure text is not transparent or hidden === */
    .stSelectbox [data-baseweb="select"] * {
        color: #1a1a1a !important;
        opacity: 1 !important;
        visibility: visible !important;
    }
    
    /* === Dropdown Menu (Popover) - High z-index === */
    [data-baseweb="popover"] {
        z-index: 999999 !important;
    }
    
    [data-baseweb="popover"] > div {
        background-color: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
    }
    
    /* === Dropdown Menu Items === */
    [data-baseweb="menu"] {
        background-color: #ffffff !important;
    }
    
    [data-baseweb="menu"] * {
        color: #1a1a1a !important;
    }
    
    [data-baseweb="menu"] li {
        color: #1a1a1a !important;
        background-color: #ffffff !important;
        padding: 10px 12px !important;
    }
    
    [data-baseweb="menu"] li:hover {
        background-color: #f1f5f9 !important;
        color: #1a1a1a !important;
    }
    
    [data-baseweb="menu"] li div,
    [data-baseweb="menu"] li span,
    [data-baseweb="menu"] li p {
        color: #1a1a1a !important;
    }
    
    /* === Listbox/Option elements === */
    [role="listbox"],
    [role="listbox"] * {
        background-color: #ffffff !important;
        color: #1a1a1a !important;
    }
    
    [role="option"] {
        color: #1a1a1a !important;
        background-color: #ffffff !important;
    }
    
    [role="option"]:hover,
    [role="option"][aria-selected="true"] {
        background-color: #eef2ff !important;
        color: #1a1a1a !important;
    }
    
    [role="option"] * {
        color: #1a1a1a !important;
    }
    
    /* === Multiselect === */
    .stMultiselect,
    .stMultiselect * {
        color: #1a1a1a !important;
    }
    
    .stMultiselect [data-baseweb="select"] > div {
        color: #1a1a1a !important;
        background-color: #ffffff !important;
    }
    
    .stMultiselect [data-baseweb="tag"] {
        background-color: #e0e7ff !important;
        color: #1a1a1a !important;
    }
    
    .stMultiselect [data-baseweb="tag"] span {
        color: #1a1a1a !important;
    }
    
    /* === Placeholder - slightly lighter === */
    .stSelectbox [data-baseweb="select"] [class*="placeholder"],
    .stSelectbox [class*="placeholder"] {
        color: #64748b !important;
    }
    
    /* === Arrow/Icon === */
    .stSelectbox [data-baseweb="select"] svg {
        fill: #475569 !important;
        color: #475569 !important;
    }
    
    /* === Radio and Checkbox === */
    .stRadio,
    .stRadio *,
    .stCheckbox,
    .stCheckbox * {
        color: #1a1a1a !important;
    }
    
    .stRadio label,
    .stRadio label span,
    .stCheckbox label,
    .stCheckbox label span {
        color: #1a1a1a !important;
    }
    
    .stRadio div[role="radiogroup"] label {
        color: #1a1a1a !important;
    }
    
    /* === Slider labels === */
    .stSlider label,
    .stSlider span {
        color: #1a1a1a !important;
    }
    
    /* =========================================
       TABLES & DATAFRAMES - RESPONSIVE
       ========================================= */
    
    /* Container styling */
    .stDataFrame,
    div[data-testid="stDataFrame"] {
        border-radius: var(--radius-lg);
        overflow: hidden;
        border: 2px solid var(--neutral-300) !important;
        box-shadow: var(--shadow-md);
        background: white !important;
        width: 100%;
    }
    
    .stDataFrame > div,
    div[data-testid="stDataFrame"] > div {
        overflow-x: visible; /* No horizontal scroll */
        width: 100%;
    }
    
    /* Force table to be visible with borders */
    .stDataFrame table,
    div[data-testid="stDataFrame"] table,
    .dataframe {
        border-collapse: separate !important;
        border-spacing: 0 !important;
        width: 100% !important;
        table-layout: auto !important; /* Auto adjust column widths */
        background: white !important;
        border: 1px solid var(--neutral-300) !important;
    }
    
    /* Table Headers - Always visible and bold */
    .stDataFrame thead tr th,
    div[data-testid="stDataFrame"] thead tr th,
    .dataframe thead tr th {
        background: var(--gradient-primary) !important;
        color: white !important;
        font-weight: 700 !important;
        font-size: var(--text-base) !important;
        padding: 12px 16px !important;
        border: 1px solid rgba(255,255,255,0.2) !important;
        border-bottom: 2px solid rgba(255,255,255,0.4) !important;
        text-align: left !important;
        white-space: normal !important; /* Allow header text wrapping */
        word-wrap: break-word !important;
        overflow-wrap: break-word !important;
        position: sticky !important;
        top: 0 !important;
        z-index: 10 !important;
        line-height: 1.3 !important;
    }
    
    /* Table Body Cells - Clear borders and readable text */
    .stDataFrame tbody tr td,
    div[data-testid="stDataFrame"] tbody tr td,
    .dataframe tbody tr td {
        color: var(--neutral-900) !important;
        font-size: var(--text-base) !important;
        padding: 10px 16px !important;
        border: 1px solid var(--neutral-200) !important;
        background: white !important;
        white-space: normal !important; /* Allow text wrapping by default */
        word-wrap: break-word !important;
        overflow-wrap: break-word !important;
        vertical-align: middle !important;
        line-height: 1.5 !important;
    }
    
    /* Zebra striping for better readability */
    .stDataFrame tbody tr:nth-child(even) td,
    div[data-testid="stDataFrame"] tbody tr:nth-child(even) td,
    .dataframe tbody tr:nth-child(even) td {
        background: var(--neutral-50) !important;
    }
    
    /* Row hover effect */
    .stDataFrame tbody tr:hover td,
    div[data-testid="stDataFrame"] tbody tr:hover td,
    .dataframe tbody tr:hover td {
        background: var(--primary-50) !important;
        cursor: pointer;
        transition: background var(--transition-fast);
    }
    
    /* RESPONSIVE TABLE ADJUSTMENTS */
    
    /* Desktop and Tablet - Full size */
    @media (min-width: 769px) {
        .stDataFrame thead tr th,
        div[data-testid="stDataFrame"] thead tr th,
        .dataframe thead tr th {
            font-size: var(--text-base) !important;
            padding: 14px 18px !important;
        }
        
        .stDataFrame tbody tr td,
        div[data-testid="stDataFrame"] tbody tr td,
        .dataframe tbody tr td {
            font-size: var(--text-base) !important;
            padding: 12px 18px !important;
        }
    }
    
    /* Tablet - Slightly smaller but still readable, with wrapping */
    @media (min-width: 481px) and (max-width: 768px) {
        .stDataFrame table,
        div[data-testid="stDataFrame"] table,
        .dataframe {
            table-layout: auto !important;
            width: 100% !important;
        }
        
        .stDataFrame thead tr th,
        div[data-testid="stDataFrame"] thead tr th,
        .dataframe thead tr th {
            font-size: var(--text-sm) !important;
            padding: 10px 12px !important;
            white-space: normal !important;
            word-wrap: break-word !important;
        }
        
        .stDataFrame tbody tr td,
        div[data-testid="stDataFrame"] tbody tr td,
        .dataframe tbody tr td {
            font-size: var(--text-sm) !important;
            padding: 8px 12px !important;
            white-space: normal !important;
            word-wrap: break-word !important;
            line-height: 1.4 !important;
        }
    }
    
    /* Mobile - Full width, no horizontal scroll */
    @media (max-width: 480px) {
        .stDataFrame,
        div[data-testid="stDataFrame"] {
            font-size: 14px !important; /* Force minimum readable size */
            width: 100% !important;
        }
        
        .stDataFrame table,
        div[data-testid="stDataFrame"] table,
        .dataframe {
            table-layout: auto !important;
            width: 100% !important;
        }
        
        .stDataFrame thead tr th,
        div[data-testid="stDataFrame"] thead tr th,
        .dataframe thead tr th {
            font-size: 13px !important; /* Slightly smaller for mobile */
            padding: 6px 8px !important;
            white-space: normal !important; /* Allow wrapping */
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
            max-width: none !important;
        }
        
        .stDataFrame tbody tr td,
        div[data-testid="stDataFrame"] tbody tr td,
        .dataframe tbody tr td {
            font-size: 12px !important; /* Compact but readable */
            padding: 6px 8px !important;
            white-space: normal !important; /* Allow wrapping */
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
            max-width: none !important;
            line-height: 1.4 !important;
        }
        
        /* No horizontal scroll - fit to container */
        .stDataFrame > div,
        div[data-testid="stDataFrame"] > div {
            overflow-x: visible !important;
            width: 100% !important;
        }
    }
    
    /* Ensure table borders are always visible */
    .stDataFrame *,
    div[data-testid="stDataFrame"] *,
    .dataframe * {
        border-color: var(--neutral-300) !important;
    }
    
    /* Fix for streamlit's default table hiding borders */
    .stDataFrame [data-testid="StyledDataFrameContainer"],
    div[data-testid="stDataFrame"] [data-testid="StyledDataFrameContainer"] {
        border: 2px solid var(--neutral-300) !important;
    }
    
    /* =========================================
       METRICS - RESPONSIVE
       ========================================= */
    
    .stMetric {
        background: var(--glass-bg);
        backdrop-filter: blur(10px);
        border-radius: var(--radius-lg);
        padding: var(--space-md);
        border: 1px solid var(--neutral-200);
        transition: all var(--transition-normal);
    }
    
    .stMetric:hover {
        box-shadow: var(--shadow-md);
    }
    
    .stMetricValue {
        font-size: var(--text-2xl) !important;
        font-weight: 700 !important;
        color: var(--neutral-900) !important;
    }
    
    .stMetricLabel {
        font-size: var(--text-sm) !important;
        color: var(--neutral-500) !important;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    /* =========================================
       ALERTS - STREAMLIT ALERTS
       ========================================= */
    
    .stAlert {
        border-radius: var(--radius-lg);
        border: none;
        padding: var(--space-md);
        font-size: var(--text-sm);
    }
    
    /* =========================================
       PROGRESS BAR - PREMIUM
       ========================================= */
    
    .stProgress > div > div {
        background: var(--gradient-primary);
        border-radius: var(--radius-full);
    }
    

    
    /* =========================================
       EXPANDER - RESPONSIVE
       ========================================= */
    
    .streamlit-expanderHeader {
        font-size: var(--text-base);
        font-weight: 600;
        color: var(--neutral-700);
        padding: var(--space-md);
        border-radius: var(--radius-md);
        transition: all var(--transition-fast);
    }
    
    .streamlit-expanderHeader:hover {
        background: var(--neutral-100);
    }
    
    /* =========================================
       MEDIA QUERIES - MOBILE FIRST
       ========================================= */
    
    /* Mobile - Base (up to 480px) */
    @media (max-width: 480px) {
        .main .block-container {
            padding: var(--space-sm) !important;
        }
        
        h1 {
            font-size: 1.5rem !important;
        }
        
        h2 {
            font-size: 1.25rem !important;
        }
        
        h3 {
            font-size: 1.1rem !important;
        }
        
        /* Stack columns on mobile */
        [data-testid="column"] {
            width: 100% !important;
            flex: 1 1 100% !important;
            min-width: 100% !important;
        }
        
        /* Sidebar adjustments */
        [data-testid="stSidebar"] {
            width: 85vw !important;
            max-width: 300px !important;
            z-index: 99990 !important; /* Ensure it is on top but below popovers */
            box-shadow: 5px 0 25px rgba(0,0,0,0.2) !important;
        }
        
        /* Ensure seamless closing */
        [data-testid="stSidebar"][aria-expanded="false"] {
            margin-left: -100% !important; /* Force off-screen if needed */
        }
        
        /* Smaller card padding on mobile */
        .metric-card, .premium-card, .alert-card {
            padding: var(--space-md);
        }
        
        /* Full width buttons */
        .stButton > button,
        .stDownloadButton > button {
            width: 100% !important;
        }
        
        /* Tabs scroll horizontally */
        .stTabs [data-baseweb="tab-list"] {
            flex-wrap: nowrap;
            justify-content: flex-start;
        }
        
        .stTabs [data-baseweb="tab"] {
            padding: var(--space-xs) var(--space-sm);
            font-size: var(--text-xs);
        }
        
        /* DataFrames scroll */
        .stDataFrame {
            font-size: var(--text-xs);
        }
    }
    
    /* Tablet (481px - 768px) */
    @media (min-width: 481px) and (max-width: 768px) {
        .main .block-container {
            padding: var(--space-md) !important;
        }
        
        /* 2 columns on tablet */
        [data-testid="column"] {
            flex: 1 1 calc(50% - var(--space-sm)) !important;
            min-width: calc(50% - var(--space-sm)) !important;
        }
        
        /* Sidebar width */
        [data-testid="stSidebar"] {
            width: 280px !important;
        }
    }
    
    /* Desktop (769px - 1440px) */
    @media (min-width: 769px) and (max-width: 1440px) {
        .main .block-container {
            padding: var(--space-lg) var(--space-xl) !important;
            max-width: 1400px !important;
            margin: 0 auto !important;
        }
        
        /* Sidebar hover effect */
        [data-testid="stSidebar"]:hover {
            box-shadow: var(--shadow-xl);
        }
    }
    
    /* Large/TV (1441px+) */
    @media (min-width: 1441px) {
        .main .block-container {
            padding: var(--space-xl) var(--space-2xl) !important;
            max-width: 1600px !important;
            margin: 0 auto !important;
        }
        
        h1 {
            font-size: 2.5rem !important;
        }
        
        h2 {
            font-size: 1.75rem !important;
        }
        
        /* Larger cards on TV */
        .metric-card, .premium-card {
            padding: var(--space-xl);
        }
        
        .stMetricValue {
            font-size: 2.5rem !important;
        }
        
        /* Larger touch targets for TV remote */
        .stButton > button,
        .stDownloadButton > button {
            min-height: 56px;
            font-size: var(--text-lg) !important;
        }
        
        /* Sidebar width for large screens */
        [data-testid="stSidebar"] {
            width: 320px !important;
        }
    }
    
    /* =========================================
       ANIMATIONS
       ========================================= */
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
    
    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }
    
    .animate-fadeIn {
        animation: fadeIn var(--transition-normal) ease-out;
    }
    
    .animate-pulse {
        animation: pulse 2s ease-in-out infinite;
    }
    
    /* Loading shimmer effect */
    .shimmer {
        background: linear-gradient(90deg, var(--neutral-100) 0%, var(--neutral-50) 50%, var(--neutral-100) 100%);
        background-size: 200% 100%;
        animation: shimmer 1.5s ease-in-out infinite;
    }
    
    /* =========================================
       UTILITY CLASSES
       ========================================= */
    
    .text-center { text-align: center; }
    .text-left { text-align: left; }
    .text-right { text-align: right; }
    
    .flex { display: flex; }
    .flex-wrap { flex-wrap: wrap; }
    .flex-col { flex-direction: column; }
    
    .items-center { align-items: center; }
    .justify-between { justify-content: space-between; }
    .gap-sm { gap: var(--space-sm); }
    .gap-md { gap: var(--space-md); }
    
    .w-full { width: 100%; }
    .h-full { height: 100%; }
    
    .rounded-lg { border-radius: var(--radius-lg); }
    .rounded-xl { border-radius: var(--radius-xl); }
    
    .shadow-md { box-shadow: var(--shadow-md); }
    .shadow-lg { box-shadow: var(--shadow-lg); }
    
    /* Hide on specific breakpoints */
    @media (max-width: 480px) {
        .hide-mobile { display: none !important; }
    }
    
    @media (min-width: 481px) and (max-width: 768px) {
        .hide-tablet { display: none !important; }
    }
    
    @media (min-width: 769px) {
        .hide-desktop { display: none !important; }
    }
    
    /* =========================================
       SCROLLBAR STYLING
       ========================================= */
    
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: var(--neutral-100);
        border-radius: var(--radius-full);
    }
    
    ::-webkit-scrollbar-thumb {
        background: var(--neutral-300);
        border-radius: var(--radius-full);
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: var(--neutral-400);
    }
    
    /* Firefox */
    * {
        scrollbar-width: thin;
        scrollbar-color: var(--neutral-300) var(--neutral-100);
    }
    
    /* =========================================
       CRITICAL FIX: SELECTBOX TEXT VISIBILITY
       This section MUST be at the end for highest priority
       ========================================= */
    
    /* Reset all selectbox text to black - MAXIMUM PRIORITY */
    div[data-testid="stSelectbox"] * {
        color: #1a1a1a !important;
    }
    
    div[data-testid="stSelectbox"] > div > div {
        color: #1a1a1a !important;
        background: #ffffff !important;
    }
    
    /* Target the inner value display */
    div[data-testid="stSelectbox"] [data-baseweb="select"] {
        color: #1a1a1a !important;
    }
    
    div[data-testid="stSelectbox"] [data-baseweb="select"] > div {
        color: #1a1a1a !important;
    }
    
    div[data-testid="stSelectbox"] [data-baseweb="select"] > div > div {
        color: #1a1a1a !important;
    }
    
    div[data-testid="stSelectbox"] [data-baseweb="select"] > div > div > div {
        color: #1a1a1a !important;
    }
    
    /* Force the selected value text to show */
    div[data-testid="stSelectbox"] [data-baseweb="select"] span {
        color: #1a1a1a !important;
        -webkit-text-fill-color: #1a1a1a !important;
    }
    
    /* Target value container specifically */
    div[data-testid="stSelectbox"] div[class*="css"] {
        color: #1a1a1a !important;
    }
    
    /* Dropdown options */
    ul[role="listbox"] li {
        color: #1a1a1a !important;
        background: #ffffff !important;
    }
    
    ul[role="listbox"] li:hover {
        background: #f1f5f9 !important;
    }
    
    /* Fix webkit text fill for Safari/Chrome */
    div[data-testid="stSelectbox"] * {
        -webkit-text-fill-color: #1a1a1a !important;
    }
    
    /* Ensure text is not transparent */
    .stSelectbox div,
    .stSelectbox span,
    .stSelectbox p {
        color: #1a1a1a !important;
        -webkit-text-fill-color: #1a1a1a !important;
        opacity: 1 !important;
    }
    
    </style>
    
    <script>
    // Auto-close sidebar on navigation - Full collapse
    (function() {
        // Function to fully close/collapse sidebar
        function closeSidebar() {
            // Try multiple methods to ensure sidebar closes
            
            // Method 1: Click the collapse button
            const collapseBtn = document.querySelector('button[kind="header"]');
            if (collapseBtn) {
                collapseBtn.click();
                return;
            }
            
            // Method 2: Find button with chevron icon (Streamlit's collapse button)
            const chevronButtons = document.querySelectorAll('button[data-testid*="base"]');
            for (let btn of chevronButtons) {
                const svg = btn.querySelector('svg');
                if (svg && btn.closest('[data-testid="stSidebar"]')) {
                    btn.click();
                    return;
                }
            }
            
            // Method 3: Direct manipulation - hide sidebar
            const sidebar = document.querySelector('[data-testid="stSidebar"]');
            if (sidebar) {
                // Set aria-expanded to false
                sidebar.setAttribute('aria-expanded', 'false');
                
                // Add collapsed state via transform
                sidebar.style.transform = 'translateX(-100%)';
                sidebar.style.transition = 'transform 0.3s ease';
                
                // Also try to find and click any collapse control
                const controls = sidebar.querySelectorAll('button');
                controls.forEach(btn => {
                    if (btn.getAttribute('aria-label')?.includes('Close') || 
                        btn.getAttribute('aria-label')?.includes('collapse')) {
                        btn.click();
                    }
                });
            }
        }
        
        // Function to restore sidebar visibility (for desktop)
        function restoreSidebar() {
            const sidebar = document.querySelector('[data-testid="stSidebar"]');
            if (sidebar && window.innerWidth > 768) {
                sidebar.style.transform = '';
                sidebar.setAttribute('aria-expanded', 'true');
            }
        }
        
        // Setup navigation listeners
        function setupNavigationListeners() {
            // Target all navigation links in sidebar
            const navLinks = document.querySelectorAll(
                '[data-testid="stSidebarNav"] a, ' +
                '[data-testid="stSidebarNav"] button, ' +
                '[data-testid="stSidebarNav"] [role="button"], ' +
                'section[data-testid="stSidebar"] a'
            );
            
            navLinks.forEach(link => {
                // Remove old listener if exists
                link.removeEventListener('click', handleNavClick);
                // Add new listener
                link.addEventListener('click', handleNavClick);
            });
        }
        
        // Navigation click handler
        function handleNavClick(e) {
            // Close sidebar after short delay
            setTimeout(function() {
                closeSidebar();
                
                // Re-open on desktop after navigation completes
                setTimeout(function() {
                    if (window.innerWidth > 768) {
                        restoreSidebar();
                    }
                }, 800);
            }, 150);
        }
        
        // Initialize on DOM ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                setupNavigationListeners();
            });
        } else {
            setupNavigationListeners();
        }
        
        // Re-initialize after Streamlit reruns
        const observer = new MutationObserver(function(mutations) {
            setupNavigationListeners();
        });
        
        // Start observing
        const startObserving = setInterval(function() {
            const sidebar = document.querySelector('[data-testid="stSidebar"]');
            if (sidebar) {
                observer.observe(document.body, {
                    childList: true,
                    subtree: true
                });
                clearInterval(startObserving);
            }
        }, 100);
        
        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                restoreSidebar();
            }
        });
    })();
    </script>
    """, unsafe_allow_html=True)


def load_input_css():
    """
    Inject CSS global untuk semua elemen input agar konsisten:
    - Background: Putih (#FFFFFF)
    - Teks: Hitam (#000000)
    - Z-index: Tinggi untuk dropdown/popover
    - Target: Selectbox, Text Input, Number Input, Text Area, Date/Time Input, Radio, Checkbox, Multiselect
    """
    st.markdown("""
    <style>
    /* =====================================================
       GLOBAL INPUT STYLING OVERRIDE (FORCE LIGHT MODE)
       ===================================================== */
    
    /* === 1. GENERAL INPUT CONTAINER (Text, Number, Date, Area) === */
    .stTextInput > div > div,
    .stNumberInput > div > div,
    .stTextArea > div > div,
    .stDateInput > div > div,
    .stTimeInput > div > div,
    .stSelectbox > div > div,
    .stMultiselect > div > div {
        background-color: #FFFFFF !important;
        color: #000000 !important;
        border-color: #E2E8F0 !important;
    }

    /* === 2. INPUT TEXT FIELDS (Actual typing area) === */
    input[type="text"],
    input[type="number"],
    input[type="date"],
    input[type="time"],
    textarea {
        background-color: #FFFFFF !important;
        color: #000000 !important;
        -webkit-text-fill-color: #000000 !important;
        caret-color: #000000 !important;
    }

    /* Placeholder Styling */
    ::placeholder {
        color: #64748B !important;
        -webkit-text-fill-color: #64748B !important;
        opacity: 1 !important;
    }

    /* === 3. SELECTBOX & MULTISELECT SPECIFICS === */
    
    /* The displayed value box */
    .stSelectbox [data-baseweb="select"],
    .stMultiselect [data-baseweb="select"] {
        background-color: #FFFFFF !important;
        color: #000000 !important;
    }

    .stSelectbox [data-baseweb="select"] > div,
    .stMultiselect [data-baseweb="select"] > div {
        background-color: #FFFFFF !important;
        color: #000000 !important;
    }

    /* Text inside the selection box */
    .stSelectbox [data-baseweb="select"] span,
    .stMultiselect [data-baseweb="select"] span,
    .stSelectbox [data-baseweb="select"] div,
    .stMultiselect [data-baseweb="select"] div {
        color: #000000 !important;
        -webkit-text-fill-color: #000000 !important;
    }

    /* Multiselect Tags */
    .stMultiselect [data-baseweb="tag"] {
        background-color: #E0E7FF !important;
        color: #000000 !important;
    }
    .stMultiselect [data-baseweb="tag"] span {
        color: #000000 !important;
        -webkit-text-fill-color: #000000 !important;
    }

    /* === 4. DROPDOWN POPOVERS & MENUS === */
    [data-baseweb="popover"],
    [data-baseweb="menu"],
    [role="listbox"] {
        z-index: 999999 !important;
        background-color: #FFFFFF !important;
    }

    [data-baseweb="popover"] > div {
        background-color: #FFFFFF !important;
        border: 1px solid #E2E8F0 !important;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15) !important;
    }

    /* Dropdown Options */
    [data-baseweb="menu"] li,
    [role="option"] {
        background-color: #FFFFFF !important;
        color: #000000 !important;
    }

    [data-baseweb="menu"] li:hover,
    [role="option"]:hover,
    [role="option"][aria-selected="true"] {
        background-color: #F1F5F9 !important;
        color: #000000 !important;
    }
    
    /* Option Text */
    [data-baseweb="menu"] li div,
    [role="option"] div,
    [role="option"] span {
        color: #000000 !important;
        -webkit-text-fill-color: #000000 !important;
    }

    /* === 5. RADIO BUTTONS & CHECKBOXES === */
    .stRadio label,
    .stCheckbox label {
        color: #000000 !important;
        -webkit-text-fill-color: #000000 !important;
    }
    
    .stRadio div[role="radiogroup"],
    .stCheckbox label span {
        color: #000000 !important;
    }

    /* === 6. ICONS & ARROWS === */
    [data-baseweb="select"] svg,
    .stDateInput svg,
    .stTimeInput svg {
        fill: #475569 !important;
        color: #475569 !important;
    }

    /* === 7. HELP TOOLTIPS === */
    .stTooltipIcon svg {
        fill: #64748B !important;
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
    load_input_css()  # Load styling override untuk semua input


def responsive_columns(num_cols: int = 4):
    """
    Create responsive columns that adapt to screen size.
    Returns Streamlit columns.
    """
    return st.columns(num_cols)


def sidebar_user_card(user: dict):
    """Render a premium user card in sidebar."""
    if not user:
        return
    
    st.markdown(f"""
    <div class="sidebar-user-card">
        <div style="display: flex; align-items: center; gap: var(--space-sm);">
            <div style="
                width: 40px;
                height: 40px;
                border-radius: var(--radius-full);
                background: var(--gradient-primary);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: 700;
                font-size: var(--text-lg);
            ">
                {user.get('first_name', 'U')[0].upper()}
            </div>
            <div>
                <p style="color: var(--neutral-900); font-weight: 600; margin: 0; font-size: var(--text-sm);">
                    {user.get('first_name', '')} {user.get('last_name', '')}
                </p>
                <p style="color: var(--neutral-500); font-size: var(--text-xs); margin: 0;">
                    {user.get('role', '').capitalize()}
                </p>
            </div>
        </div>
    </div>
    """, unsafe_allow_html=True)
