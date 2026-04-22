<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="d-flex align-items-center justify-content-between w-100">
            <h2 class="sidebar-title m-0" id="sidebarTitle">Dashboard</h2>
            <button id="toggleSidebar" class="btn btn-toggle">
                <i class="bi bi-list"></i>
            </button>
        </div>
    </div>

    <div class="sidebar-menu">
        <a href="/renewal-system/public/index.php"
            class="sidebar-link <?= ($activePage == 'products') ? 'active' : '' ?>" title="Products">
            <i class="bi bi-box"></i> <span class="link-text">Products</span>
        </a>

        <a href="/renewal-system/public/index.php?action=history"
            class="sidebar-link <?= ($activePage == 'history') ? 'active' : '' ?>" title="Last Renewal">
            <i class="bi bi-clock-history"></i> <span class="link-text">Last Renewal</span>
        </a>
    </div>

    <div class="sidebar-footer">
        <a href="/renewal-system/public/logout.php" class="sidebar-link logout-link" title="Logout">
            <i class="bi bi-box-arrow-right"></i> <span class="link-text">Logout</span>
        </a>
    </div>
</div>

<style>
    :root {
        --sidebar-width: 260px;
        --sidebar-collapsed-width: 85px;
        --grad-sidebar: linear-gradient(145deg, #1e3c72, #2a5298);
        --accent-color: #00d2ff;
    }

    .sidebar {
        width: var(--sidebar-width);
        height: 100vh;
        position: fixed;
        top: 0;
        left: 0;
        background: var(--grad-sidebar);
        color: white;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        display: flex;
        flex-direction: column;
        z-index: 1000;
        box-shadow: 10px 0 30px rgba(0, 0, 0, 0.1);
        border-right: 1px solid rgba(255,255,255,0.1);
    }

    .sidebar.collapsed {
        width: var(--sidebar-collapsed-width);
    }

    .sidebar-header {
        padding: 25px 20px;
        display: flex;
        align-items: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar-title {
        font-weight: 800;
        font-size: 1.2rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        background: linear-gradient(to right, #fff, #00d2ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .btn-toggle {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        border: 1px solid rgba(255,255,255,0.2);
        border-radius: 10px;
        padding: 5px 12px;
        backdrop-filter: blur(5px);
    }

    .btn-toggle:hover {
        background: var(--accent-color);
        color: #1e3c72;
    }

    .sidebar-menu {
        padding: 20px 15px;
        flex-grow: 1;
    }

    .sidebar-link {
        display: flex;
        align-items: center;
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        padding: 14px 18px;
        margin-bottom: 10px;
        border-radius: 15px;
        transition: all 0.3s ease;
    }

    .sidebar-link i {
        font-size: 1.2rem;
        min-width: 35px;
    }

    .sidebar-link:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        transform: translateX(8px);
    }

    .sidebar-link.active {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar-footer {
        padding: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .logout-link {
        background: rgba(255, 71, 87, 0.1);
        color: #ff4757;
        border: 1px solid rgba(255, 71, 87, 0.2);
    }

    .logout-link:hover {
        background: #ff4757;
        color: white;
    }

    .sidebar.collapsed .link-text, 
    .sidebar.collapsed .sidebar-title {
        display: none;
    }
    .sidebar.collapsed .sidebar-header {
        justify-content: center;
        padding: 25px 10px;
    }
</style>