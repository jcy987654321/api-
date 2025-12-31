/**
 * Admin Panel JavaScript
 * Handles sidebar, theme, search, and other interactive features
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initSidebar();
    initTheme();
    initMenuSearch();
    initQuickSearch();
    initNotifications();
    initSubmenuToggles();
});

/**
 * Sidebar Management
 */
function initSidebar() {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebarToggleMobile = document.getElementById('sidebar-toggle-mobile');
    const sidebarOverlay = document.getElementById('sidebar-overlay');

    // Desktop sidebar toggle
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
        });
    }

    // Mobile sidebar toggle
    if (sidebarToggleMobile) {
        sidebarToggleMobile.addEventListener('click', function() {
            sidebar.classList.toggle('show');
            if (sidebarOverlay) {
                sidebarOverlay.classList.toggle('show');
            }
            document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
        });
    }

    // Overlay click to close sidebar
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
            document.body.style.overflow = '';
        });
    }

    // Restore sidebar state
    const savedState = localStorage.getItem('sidebarCollapsed');
    if (savedState === 'true' && window.innerWidth >= 1200) {
        sidebar.classList.add('collapsed');
    }

    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (window.innerWidth >= 1200) {
                sidebar.classList.remove('show');
                if (sidebarOverlay) {
                    sidebarOverlay.classList.remove('show');
                }
                document.body.style.overflow = '';
            } else {
                sidebar.classList.remove('collapsed');
            }
        }, 250);
    });
}

/**
 * Theme Management
 */
function initTheme() {
    const themeButtons = document.querySelectorAll('[data-theme]');
    const themeIcon = document.getElementById('theme-icon');
    const html = document.documentElement;

    // Get saved theme or default to 'light'
    let currentTheme = localStorage.getItem('adminTheme') || 'light';

    // Set initial theme
    setTheme(currentTheme);

    // Theme button click handlers
    themeButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const theme = this.getAttribute('data-theme');
            setTheme(theme);
            localStorage.setItem('adminTheme', theme);
        });
    });

    // Check for system preference in auto mode
    function applyTheme(theme) {
        if (theme === 'auto') {
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            html.classList.toggle('dark', prefersDark);
            html.classList.toggle('light', !prefersDark);
        } else {
            html.classList.remove('light', 'dark');
            html.classList.add(theme);
        }
        updateThemeIcon(theme);
    }

    function setTheme(theme) {
        currentTheme = theme;
        applyTheme(theme);
    }

    function updateThemeIcon(theme) {
        if (themeIcon) {
            if (theme === 'dark' || (theme === 'auto' && html.classList.contains('dark'))) {
                themeIcon.classList.remove('bi-moon-stars');
                themeIcon.classList.add('bi-sun');
            } else {
                themeIcon.classList.remove('bi-sun');
                themeIcon.classList.add('bi-moon-stars');
            }
        }
    }

    // Listen for system theme changes
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
        if (currentTheme === 'auto') {
            applyTheme('auto');
        }
    });
}

/**
 * Menu Search
 */
function initMenuSearch() {
    const searchInput = document.getElementById('menu-search');
    const sidebarMenu = document.getElementById('sidebar-menu');

    if (!searchInput || !sidebarMenu) return;

    searchInput.addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase().trim();
        const menuItems = sidebarMenu.querySelectorAll('.nav-item');

        menuItems.forEach(item => {
            const link = item.querySelector('.nav-link');
            const text = link.textContent.toLowerCase();
            const submenu = item.querySelector('.submenu');

            if (query === '') {
                // Show all items when search is empty
                item.style.display = '';
                if (submenu) {
                    submenu.classList.remove('show');
                    item.querySelector('.submenu-toggle')?.classList.remove('open');
                }
            } else if (text.includes(query)) {
                // Show matching items
                item.style.display = '';

                // If it's in a submenu, show the parent submenu
                const parentSubmenu = item.closest('.submenu');
                if (parentSubmenu) {
                    const parentItem = parentSubmenu.closest('.nav-item');
                    parentSubmenu.classList.add('show');
                    parentItem.querySelector('.submenu-toggle')?.classList.add('open');
                    parentItem.style.display = '';
                }
            } else if (submenu) {
                // Check if any child items match
                const childMatches = Array.from(submenu.querySelectorAll('.nav-item'))
                    .some(child => child.querySelector('.nav-link').textContent.toLowerCase().includes(query));

                if (childMatches) {
                    item.style.display = '';
                    submenu.classList.add('show');
                    item.querySelector('.submenu-toggle')?.classList.add('open');
                } else {
                    item.style.display = 'none';
                }
            } else {
                // Hide non-matching leaf items
                item.style.display = 'none';
            }
        });
    });
}

/**
 * Quick Search
 */
function initQuickSearch() {
    const desktopSearch = document.getElementById('desktop-search-input');
    const mobileSearch = document.getElementById('mobile-search-input');
    const desktopForm = document.getElementById('desktop-search-form');
    const mobileForm = document.getElementById('mobile-search-form');

    function handleSearch(e, form) {
        const query = e.target.value.trim();

        if (e.key === 'Enter' && query) {
            e.preventDefault();
            // Redirect to search page or perform search
            window.location.href = `/admin/search?q=${encodeURIComponent(query)}`;
        }
    }

    if (desktopSearch && desktopForm) {
        desktopSearch.addEventListener('keypress', (e) => handleSearch(e, desktopForm));
    }

    if (mobileSearch && mobileForm) {
        mobileSearch.addEventListener('keypress', (e) => handleSearch(e, mobileForm));
    }
}

/**
 * Notifications
 */
function initNotifications() {
    const notificationBadge = document.getElementById('notification-badge');
    const notificationDropdown = document.querySelector('.notification-dropdown');

    if (!notificationBadge || !notificationDropdown) return;

    // Simulate checking notifications
    notificationDropdown.addEventListener('shown.bs.dropdown', function() {
        // Mark notifications as read
        notificationBadge.style.display = 'none';
    });

    // Check for new notifications periodically
    function checkNotifications() {
        // This would typically be an AJAX call
        // For demo purposes, we'll just check localStorage
        const unreadCount = localStorage.getItem('unreadNotifications');
        if (unreadCount && parseInt(unreadCount) > 0) {
            notificationBadge.textContent = unreadCount;
            notificationBadge.style.display = 'inline-flex';
        }
    }

    // Check on load
    checkNotifications();

    // Check every 30 seconds
    setInterval(checkNotifications, 30000);
}

/**
 * Submenu Toggles
 */
function initSubmenuToggles() {
    const toggleLinks = document.querySelectorAll('.submenu-toggle');

    toggleLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const parent = this.closest('.nav-item');
            const submenu = parent.querySelector('.submenu');

            if (submenu) {
                parent.classList.toggle('open');
                submenu.classList.toggle('show');

                // Rotate arrow
                const arrow = this.querySelector('.submenu-arrow');
                if (arrow) {
                    arrow.style.transform = parent.classList.contains('open') ? 'rotate(180deg)' : 'rotate(0)';
                }
            }
        });
    });
}

/**
 * Utility Functions
 */

// Format number with commas
function formatNumber(num) {
    if (num === null || num === undefined) return '--';
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

// Format percentage
function formatPercentage(value) {
    if (value === null || value === undefined) return '--';
    return `${value.toFixed(1)}%`;
}

// Format bytes
function formatBytes(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

// Debounce function
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Show toast notification
function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toast-container') || createToastContainer();

    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');

    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;

    toastContainer.appendChild(toast);

    const bsToast = new bootstrap.Toast(toast, {
        delay: 5000
    });

    bsToast.show();

    // Remove toast after hidden
    toast.addEventListener('hidden.bs.toast', function() {
        toast.remove();
    });
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
    container.style.zIndex = '1100';
    document.body.appendChild(container);
    return container;
}

// Show confirm dialog
function showConfirm(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// Copy to clipboard
function copyToClipboard(text) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(() => {
            showToast('Copied to clipboard!', 'success');
        }).catch(() => {
            fallbackCopyToClipboard(text);
        });
    } else {
        fallbackCopyToClipboard(text);
    }
}

function fallbackCopyToClipboard(text) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    document.body.appendChild(textarea);
    textarea.select();
    try {
        document.execCommand('copy');
        showToast('Copied to clipboard!', 'success');
    } catch (err) {
        showToast('Failed to copy', 'danger');
    }
    document.body.removeChild(textarea);
}

// Export functions for global use
window.AdminJS = {
    formatNumber,
    formatPercentage,
    formatBytes,
    debounce,
    showToast,
    showConfirm,
    copyToClipboard
};
