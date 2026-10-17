/**
 * Probashi Kormo — Admin panel behaviour.
 * Only layout-level features live here: theme, sidebar, dropdowns and toasts.
 * Page features (tables, forms, modals) are handled by Livewire components.
 */

const THEME_KEY = 'pk-admin-theme-v2';
const SIDEBAR_KEY = 'pk-admin-sidebar-collapsed';
const MOBILE_QUERY = '(max-width:900px)';

/* ---------- Safe localStorage ---------- */
function readStorage(key) {
    try {
        return localStorage.getItem(key);
    } catch (error) {
        return null;
    }
}

function writeStorage(key, value) {
    try {
        localStorage.setItem(key, value);
    } catch (error) {
        /* Storage can be blocked (private mode). The UI still works without it. */
    }
}

/* ---------- Theme ---------- */
function applyTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);

    const icon = document.getElementById('themeIcon');
    if (icon) {
        icon.className = theme === 'light' ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';
    }

    const button = document.getElementById('themeToggleBtn');
    if (button) {
        button.setAttribute('aria-pressed', theme === 'light' ? 'true' : 'false');
    }

    writeStorage(THEME_KEY, theme);
}

function wireTheme() {
    applyTheme(readStorage(THEME_KEY) || 'light');

    const button = document.getElementById('themeToggleBtn');
    if (!button) {
        return;
    }

    button.addEventListener('click', () => {
        const current = document.documentElement.getAttribute('data-theme') || 'light';
        applyTheme(current === 'dark' ? 'light' : 'dark');
    });
}

/* ---------- Toasts ---------- */
const TOAST_ICONS = {
    ok: 'bi-check-circle-fill',
    error: 'bi-x-circle-fill',
    warn: 'bi-exclamation-triangle-fill',
};

function removeToast(element) {
    clearTimeout(Number(element.dataset.timer));
    element.style.transition = '.2s';
    element.style.opacity = '0';
    element.style.transform = 'translateY(6px)';
    setTimeout(() => element.remove(), 200);
}

function toast(message, type = 'ok') {
    const wrap = document.getElementById('toastWrap');
    if (!wrap) {
        return;
    }

    const kind = TOAST_ICONS[type] ? type : 'ok';

    const element = document.createElement('div');
    element.className = `toast-item glass ${kind}`;
    element.setAttribute('role', kind === 'error' ? 'alert' : 'status');
    element.setAttribute('aria-live', kind === 'error' ? 'assertive' : 'polite');

    const icon = document.createElement('i');
    icon.className = `bi ${TOAST_ICONS[kind]} ti-icon`;

    const text = document.createElement('span');
    text.textContent = message;

    const close = document.createElement('button');
    close.type = 'button';
    close.className = 'ti-close';
    close.setAttribute('aria-label', 'Dismiss notification');
    const closeIcon = document.createElement('i');
    closeIcon.className = 'bi bi-x';
    close.appendChild(closeIcon);
    close.addEventListener('click', () => removeToast(element));

    element.append(icon, text, close);
    wrap.appendChild(element);
    element.dataset.timer = String(setTimeout(() => removeToast(element), 4000));
}

/* ---------- Sidebar: mobile drawer + desktop collapse ---------- */
function toggleSidebarCollapse(force) {
    const shell = document.querySelector('.app-shell');
    if (!shell) {
        return;
    }

    const collapsed = typeof force === 'boolean' ? force : !shell.classList.contains('sidebar-collapsed');
    shell.classList.toggle('sidebar-collapsed', collapsed);
    writeStorage(SIDEBAR_KEY, collapsed ? '1' : '0');

    const hamburger = document.getElementById('hamburgerBtn');
    if (hamburger) {
        hamburger.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
    }
}

function wireSidebar() {
    const hamburger = document.getElementById('hamburgerBtn');
    const sidebar = document.querySelector('.sidebar');
    const scrim = document.getElementById('sidebarScrim');
    if (!hamburger || !sidebar || !scrim) {
        return;
    }

    if (readStorage(SIDEBAR_KEY) === '1') {
        toggleSidebarCollapse(true);
    }

    const isMobile = () => window.matchMedia(MOBILE_QUERY).matches;

    const open = () => {
        sidebar.classList.add('open');
        scrim.classList.add('open');
        hamburger.setAttribute('aria-expanded', 'true');
    };

    const close = () => {
        sidebar.classList.remove('open');
        scrim.classList.remove('open');
        hamburger.setAttribute('aria-expanded', 'false');
    };

    hamburger.addEventListener('click', () => {
        if (isMobile()) {
            sidebar.classList.contains('open') ? close() : open();
        } else {
            toggleSidebarCollapse();
        }
    });

    scrim.addEventListener('click', close);
    sidebar.querySelectorAll('a').forEach((link) => link.addEventListener('click', close));
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            close();
        }
    });
}

/* ---------- Dropdowns ---------- */
function closeAllDropdowns() {
    document.querySelectorAll('.dropdown-panel.open').forEach((panel) => {
        panel.classList.remove('open');
        const button = document.querySelector(`[aria-controls="${panel.id}"]`);
        if (button) {
            button.setAttribute('aria-expanded', 'false');
        }
    });
}

function wireDropdown(buttonId, panelId) {
    const button = document.getElementById(buttonId);
    const panel = document.getElementById(panelId);
    if (!button || !panel) {
        return;
    }

    button.setAttribute('aria-haspopup', 'true');
    button.setAttribute('aria-expanded', 'false');
    button.setAttribute('aria-controls', panelId);

    button.addEventListener('click', (event) => {
        event.stopPropagation();
        const willOpen = !panel.classList.contains('open');
        closeAllDropdowns();
        panel.classList.toggle('open', willOpen);
        button.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
        if (willOpen) {
            const first = panel.querySelector('.dd-item');
            if (first) {
                first.focus();
            }
        }
    });

    panel.addEventListener('click', (event) => event.stopPropagation());
}

/* ---------- Boot ---------- */
document.addEventListener('DOMContentLoaded', () => {
    wireTheme();
    wireSidebar();
    wireDropdown('notifBtn', 'notifPanel');
    wireDropdown('avatarBtn', 'avatarPanel');

    document.addEventListener('click', closeAllDropdowns);
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeAllDropdowns();
        }
    });
});

/* Livewire components can show a toast with: $this->dispatch('toast', message: 'Saved', type: 'ok'); */
window.addEventListener('toast', (event) => {
    const detail = event.detail || {};
    toast(detail.message ?? '', detail.type ?? 'ok');
});

window.toast = toast;

/* A message saved with session()->flash('toast', '...') before a redirect is shown once the page loads. */
document.addEventListener('DOMContentLoaded', () => {
    const flash = document.querySelector('[data-flash-toast]');
    if (flash) {
        toast(flash.getAttribute('data-flash-toast') ?? '', 'ok');
        flash.remove();
    }
});
