<script>
    // ========================================================
    // ATELIER WORKSPACE ARCHITECT & CUSTOMIZATION ENGINE
    // ========================================================

    // 1. Theme Management System
    function setTheme(theme) {
        let effectiveTheme = theme;
        if (theme === 'auto') {
            effectiveTheme = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }

        document.documentElement.setAttribute('data-bs-theme', effectiveTheme);
        localStorage.setItem('xivig-global-theme', theme);
        updateThemeUI(theme, effectiveTheme);
    }

    function updateThemeUI(selectedTheme, effectiveTheme) {
        // Quick toggle icon in header
        const icon = document.getElementById('quickThemeIcon');
        if (icon) {
            icon.className = effectiveTheme === 'dark' ? 'bi bi-moon-stars' : 'bi bi-sun';
        }

        // Offcanvas Theme Cards
        const cardDark = document.getElementById('theme-card-dark');
        const cardLight = document.getElementById('theme-card-light');
        const cardAuto = document.getElementById('theme-card-auto');
        const currentThemeLabel = document.getElementById('current-theme-label');

        [cardDark, cardLight, cardAuto].forEach(c => c && c.classList.remove('active'));

        if (selectedTheme === 'dark') {
            if (cardDark) cardDark.classList.add('active');
            if (currentThemeLabel) currentThemeLabel.textContent = 'Obsidian Noir';
        } else if (selectedTheme === 'light') {
            if (cardLight) cardLight.classList.add('active');
            if (currentThemeLabel) currentThemeLabel.textContent = 'Porcelain Couture';
        } else if (selectedTheme === 'auto') {
            if (cardAuto) cardAuto.classList.add('active');
            if (currentThemeLabel) currentThemeLabel.textContent = 'Horizon (System)';
        }
    }

    // 2. Accent Color Customization
    function setAccentColor(color, name, btnElement) {
        document.documentElement.style.setProperty('--elite-primary', color);
        document.documentElement.style.setProperty('--primary-accent', color);
        document.documentElement.style.setProperty('--admin-accent', color);
        document.documentElement.style.setProperty('--gold-primary', color);

        localStorage.setItem('xivig-accent-color', color);
        localStorage.setItem('xivig-accent-name', name || 'Custom');

        const nameDisplay = document.getElementById('accent-name-display');
        if (nameDisplay && name) {
            nameDisplay.textContent = name;
        }

        // Update active checkmarks on swatches
        document.querySelectorAll('.accent-swatch').forEach(swatch => {
            swatch.classList.remove('active');
            const check = swatch.querySelector('i');
            if (check) check.classList.add('d-none');
        });

        if (btnElement) {
            btnElement.classList.add('active');
            const check = btnElement.querySelector('i');
            if (check) check.classList.remove('d-none');
        } else {
            const match = document.querySelector(`.accent-swatch[data-color="${color}"]`);
            if (match) {
                match.classList.add('active');
                const check = match.querySelector('i');
                if (check) check.classList.remove('d-none');
            }
        }
    }

    // 3. Interface Typography Scale
    function setFontScale(scale) {
        if (scale === 100) {
            document.documentElement.style.fontSize = '';
        } else {
            document.documentElement.style.fontSize = (scale / 100 * 16) + 'px';
        }

        localStorage.setItem('xivig-font-scale', scale);

        // Update button states
        const compactBtn = document.getElementById('font-scale-compact');
        const standardBtn = document.getElementById('font-scale-standard');
        const generousBtn = document.getElementById('font-scale-generous');

        [compactBtn, standardBtn, generousBtn].forEach(b => b && b.classList.remove('active'));

        if (scale === 92 && compactBtn) compactBtn.classList.add('active');
        else if (scale === 108 && generousBtn) generousBtn.classList.add('active');
        else if (standardBtn) standardBtn.classList.add('active');
    }

    // 4. Reset All Workspace Preferences
    function resetWorkspaceDefaults() {
        localStorage.removeItem('xivig-global-theme');
        localStorage.removeItem('xivig-accent-color');
        localStorage.removeItem('xivig-accent-name');
        localStorage.removeItem('xivig-font-scale');

        setTheme('dark');
        setAccentColor('#d4af37', 'Maison Gold');
        setFontScale(100);

        // Feedback indication
        const toast = document.createElement('div');
        toast.className = 'alert alert-dark position-fixed bottom-0 end-0 m-4 shadow-lg rounded-pill px-4 py-2 text-gold border border-warning';
        toast.style.zIndex = '10060';
        toast.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i> Atelier preferences restored to default.';
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 2500);
    }

    // Backward compatibility aliases
    function resetThemeDefaults() {
        resetWorkspaceDefaults();
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Theme from localStorage
        const savedTheme = localStorage.getItem('xivig-global-theme') || 'dark';
        setTheme(savedTheme);

        // Initialize Accent Color from localStorage
        const savedAccent = localStorage.getItem('xivig-accent-color');
        const savedAccentName = localStorage.getItem('xivig-accent-name');
        if (savedAccent) {
            setAccentColor(savedAccent, savedAccentName);
        }

        // Initialize Font Scale from localStorage
        const savedFontScale = localStorage.getItem('xivig-font-scale');
        if (savedFontScale) {
            setFontScale(parseInt(savedFontScale, 10));
        }

        // Quick Theme Toggle Button in Header
        const quickToggle = document.getElementById('quickThemeToggle');
        if (quickToggle) {
            quickToggle.addEventListener('click', function(e) {
                e.preventDefault();
                const current = document.documentElement.getAttribute('data-bs-theme');
                const next = current === 'dark' ? 'light' : 'dark';
                setTheme(next);
            });
        }

        // Sidebar Mobile Toggle
        const sidebarToggle = document.getElementById('sidebar-toggle-btn');
        const sidebarClose = document.getElementById('sidebar-close');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.querySelector('.mobile-menu-overlay');

        function toggleSidebar() {
            if (sidebar) sidebar.classList.toggle('show');
            if (overlay) overlay.classList.toggle('show');
        }

        if (sidebarToggle) sidebarToggle.addEventListener('click', toggleSidebar);
        if (sidebarClose) sidebarClose.addEventListener('click', toggleSidebar);
        if (overlay) overlay.addEventListener('click', toggleSidebar);

        // Sidebar Submenu Collapsible Accordion (collapses adjacent open menus)
        document.querySelectorAll('.sidebar__item--has-dropdown > .sidebar__link').forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const parent = this.parentElement;
                const isOpening = !parent.classList.contains('show') && !parent.classList.contains('open');

                // Collapse all adjacent dropdown menus in the sidebar
                document.querySelectorAll('.sidebar__item--has-dropdown').forEach(function(other) {
                    if (other !== parent) {
                        other.classList.remove('show', 'open', 'active');
                        const otherWrapper = other.querySelector('.sidebar__submenu-wrapper');
                        if (otherWrapper) {
                            otherWrapper.style.display = 'none';
                            otherWrapper.style.gridTemplateRows = '0fr';
                        }
                    }
                });

                // Toggle the selected dropdown menu
                if (isOpening) {
                    parent.classList.add('show', 'open');
                    const wrapper = parent.querySelector('.sidebar__submenu-wrapper');
                    if (wrapper) {
                        wrapper.style.display = 'grid';
                        wrapper.style.gridTemplateRows = '1fr';
                    }
                } else {
                    parent.classList.remove('show', 'open');
                    const wrapper = parent.querySelector('.sidebar__submenu-wrapper');
                    if (wrapper) {
                        wrapper.style.display = 'none';
                        wrapper.style.gridTemplateRows = '0fr';
                    }
                }
            });
        });
    });

    // ========================================================
    // SWEETALERT2 ENTERPRISE ATELIER INTEGRATION
    // ========================================================
    window.EliteAlert = {
        toast: function(message, icon = 'success') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: icon,
                    title: message,
                    showConfirmButton: false,
                    timer: 3500,
                    timerProgressBar: true,
                    customClass: {
                        container: 'elite-swal-toast-container',
                        popup: 'elite-swal-toast'
                    }
                });
            } else {
                alert(message);
            }
        },
        alert: function(title, message, icon = 'info') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: title,
                    text: message,
                    icon: icon,
                    confirmButtonText: 'Acknowledge',
                    customClass: {
                        popup: 'elite-swal-popup',
                        confirmButton: 'btn btn-primary rounded-pill px-4 py-2'
                    },
                    buttonsStyling: false
                });
            } else {
                alert(title + "\n\n" + message);
            }
        },
        confirm: function(options, onConfirmed) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: options.title || 'Are you certain?',
                    text: options.text || 'This action cannot be undone.',
                    icon: options.icon || 'warning',
                    showCancelButton: true,
                    confirmButtonText: options.confirmButtonText || '<i class="bi bi-trash3-fill me-1"></i> Yes, Proceed',
                    cancelButtonText: options.cancelButtonText || 'Cancel',
                    reverseButtons: true,
                    focusCancel: true,
                    customClass: {
                        popup: 'elite-swal-popup',
                        confirmButton: 'btn btn-danger rounded-pill px-4 py-2 fw-bold',
                        cancelButton: 'btn btn-outline-secondary rounded-pill px-4 py-2 me-2'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed && typeof onConfirmed === 'function') {
                        onConfirmed();
                    }
                });
            } else {
                if (confirm(options.text || 'Are you certain?')) {
                    if (typeof onConfirmed === 'function') onConfirmed();
                }
            }
        }
    };

    // Auto-display Laravel flash messages via SweetAlert2
    @if(session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            EliteAlert.toast({!! json_encode(session('success')) !!}, 'success');
        });
    @endif

    @if(session('error'))
        document.addEventListener('DOMContentLoaded', function() {
            EliteAlert.alert('Action Failed', {!! json_encode(session('error')) !!}, 'error');
        });
    @endif

    @if(isset($errors) && $errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Please Review Required Fields',
                    html: {!! json_encode('<ul class="text-start small mb-0 ps-3">' . implode('', array_map(fn($e) => '<li class="py-0.5">' . e($e) . '</li>', $errors->all())) . '</ul>') !!},
                    confirmButtonText: 'Review Form',
                    customClass: {
                        popup: 'elite-swal-popup',
                        confirmButton: 'btn btn-danger rounded-pill px-4 py-2'
                    },
                    buttonsStyling: false
                });
            }
        });
    @endif

    // Global Event Interceptor: Convert all delete / purge forms & confirm() buttons across all admin sections
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (!form || form.dataset.swalBypassed) return;

            const methodInput = form.querySelector('input[name="_method"]');
            const isDeleteMethod = (methodInput && methodInput.value.toUpperCase() === 'DELETE') || (form.method && form.method.toUpperCase() === 'DELETE');
            const hasOnsubmitConfirm = form.hasAttribute('onsubmit') && form.getAttribute('onsubmit').includes('confirm');
            const isDestructive = form.action && (form.action.includes('destroy') || form.action.includes('delete') || form.action.includes('clear') || form.action.includes('purge'));

            if (isDeleteMethod || hasOnsubmitConfirm || isDestructive) {
                e.preventDefault();
                e.stopImmediatePropagation();

                let message = 'This record will be permanently purged from the atelier registry.';
                if (hasOnsubmitConfirm) {
                    const match = form.getAttribute('onsubmit').match(/confirm\(['"](.*?)['"]\)/);
                    if (match && match[1]) message = match[1];
                }

                EliteAlert.confirm({
                    title: 'Purge Confirmation',
                    text: message,
                    icon: 'warning',
                    confirmButtonText: '<i class="bi bi-trash3-fill me-1"></i> Yes, Proceed'
                }, function() {
                    form.dataset.swalBypassed = 'true';
                    form.removeAttribute('onsubmit');
                    form.submit();
                });
            }
        }, true);

        // Intercept buttons with onclick="return confirm(...)"
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('button, a');
            if (!btn || btn.dataset.swalBypassed) return;

            const hasOnclickConfirm = btn.hasAttribute('onclick') && btn.getAttribute('onclick').includes('confirm');
            if (hasOnclickConfirm) {
                e.preventDefault();
                e.stopImmediatePropagation();

                let message = 'Are you certain you wish to proceed?';
                const match = btn.getAttribute('onclick').match(/confirm\(['"](.*?)['"]\)/);
                if (match && match[1]) message = match[1];

                EliteAlert.confirm({
                    title: 'Confirmation Required',
                    text: message,
                    icon: 'warning'
                }, function() {
                    btn.dataset.swalBypassed = 'true';
                    btn.removeAttribute('onclick');
                    btn.click();
                });
            }
        }, true);
    });

    // ==========================================
    // Elite Admin Global Spotlight & Search Hub
    // ==========================================
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('adminHeaderSearchInput');
        const searchContainer = document.getElementById('adminHeaderSearchContainer');
        const searchDropdown = document.getElementById('adminSearchDropdown');
        const searchContent = document.getElementById('adminSearchDropdownContent');
        const clearBtn = document.getElementById('adminSearchClearBtn');
        const countBadge = document.getElementById('searchResultCountBadge');
        const fullLink = document.getElementById('fullSearchLink');
        const searchForm = document.getElementById('adminHeaderSearchForm');

        if (!searchInput || !searchDropdown || !searchContent) return;

        let debounceTimer = null;
        let activeIndex = -1;
        let currentItems = [];
        let currentAbortController = null;

        function escapeHtml(str) {
            if (!str) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function showDropdown() {
            searchDropdown.classList.remove('d-none');
        }

        function hideDropdown() {
            searchDropdown.classList.add('d-none');
            activeIndex = -1;
            updateActiveState();
        }

        function updateActiveState() {
            currentItems.forEach((item, index) => {
                if (index === activeIndex) {
                    item.classList.add('active');
                    item.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
                } else {
                    item.classList.remove('active');
                }
            });
        }

        function updateClearBtn() {
            if (clearBtn) {
                if (searchInput.value.trim().length > 0) {
                    clearBtn.classList.remove('d-none');
                } else {
                    clearBtn.classList.add('d-none');
                }
            }
        }

        function fetchLiveResults(query) {
            if (currentAbortController) {
                currentAbortController.abort();
            }
            currentAbortController = new AbortController();

            searchContent.innerHTML = `
                <div class="text-center py-4 text-muted small">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                    Searching editorial vault &amp; desks...
                </div>
            `;
            showDropdown();

            const url = `{{ route('admin.search') }}?query=${encodeURIComponent(query)}&live=1`;

            fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                signal: currentAbortController.signal
            })
            .then(res => res.json())
            .then(data => {
                renderResults(data, query);
            })
            .catch(err => {
                if (err.name === 'AbortError') return;
                searchContent.innerHTML = `
                    <div class="text-center py-4 text-danger small">
                        <i class="bi bi-exclamation-triangle-fill d-block mb-1 fs-5"></i>
                        Unable to complete live search. Press Enter for full directory.
                    </div>
                `;
            });
        }

        function renderResults(data, query) {
            if (countBadge) {
                countBadge.textContent = `${data.total_count} item${data.total_count === 1 ? '' : 's'}`;
            }

            if (fullLink) {
                fullLink.href = data.full_search_url || `{{ route('admin.search') }}?query=${encodeURIComponent(query)}`;
                fullLink.innerHTML = query.length > 0 
                    ? `View All Results for "<strong>${escapeHtml(query)}</strong>" &rarr;` 
                    : `Explore Entire Editorial Archive &rarr;`;
            }

            if (data.total_count === 0) {
                searchContent.innerHTML = `
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-search fs-3 opacity-25 d-block mb-2"></i>
                        <p class="small fw-semibold mb-1">No chronicles or records matched "${escapeHtml(query)}"</p>
                        <p class="extra-small text-muted mb-0">Try different keywords or press <kbd>Enter</kbd> for deep search.</p>
                    </div>
                `;
                currentItems = [];
                activeIndex = -1;
                return;
            }

            let html = '';

            // 1. Quick Navigation Shortcuts
            if (data.shortcuts && data.shortcuts.length > 0) {
                html += `<div class="admin-search-group-title"><i class="bi bi-lightning-charge text-gold me-1"></i>Quick Desks & Shortcuts</div>`;
                data.shortcuts.forEach(s => {
                    html += `
                        <a href="${s.url}" class="admin-search-item">
                            <div class="admin-search-item-icon bg-primary bg-opacity-10 text-primary">
                                <i class="bi ${escapeHtml(s.icon)}"></i>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="fw-bold small text-body text-truncate">${escapeHtml(s.title)}</div>
                                <span class="badge bg-secondary bg-opacity-10 text-secondary extra-small">${escapeHtml(s.badge)}</span>
                            </div>
                            <i class="bi bi-arrow-right text-muted extra-small"></i>
                        </a>
                    `;
                });
            }

            // 2. Chronicles / Posts
            if (data.posts && data.posts.length > 0) {
                html += `<div class="admin-search-group-title"><i class="bi bi-journal-richtext text-info me-1"></i>Chronicles &amp; Articles</div>`;
                data.posts.forEach(p => {
                    const statusClass = p.status === 'published' ? 'bg-success text-success' : 'bg-warning text-warning';
                    html += `
                        <a href="${p.url}" class="admin-search-item">
                            <div class="admin-search-item-icon bg-info bg-opacity-10 text-info">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="fw-semibold small text-body text-truncate">${escapeHtml(p.title)}</div>
                                <div class="extra-small text-muted d-flex align-items-center gap-2 mt-1">
                                    <span class="badge ${statusClass} bg-opacity-10 py-0 px-2 text-capitalize">${escapeHtml(p.status)}</span>
                                    <span class="text-truncate">${escapeHtml(p.category)}</span>
                                </div>
                            </div>
                            <i class="bi bi-pencil-square text-muted extra-small"></i>
                        </a>
                    `;
                });
            }

            // 3. Static Pages
            if (data.pages && data.pages.length > 0) {
                html += `<div class="admin-search-group-title"><i class="bi bi-layout-text-window text-primary me-1"></i>Maison Pages</div>`;
                data.pages.forEach(p => {
                    html += `
                        <a href="${p.url}" class="admin-search-item">
                            <div class="admin-search-item-icon bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-layout-text-window"></i>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="fw-semibold small text-body text-truncate">${escapeHtml(p.title)}</div>
                                <span class="extra-small text-muted">Static Page</span>
                            </div>
                            <i class="bi bi-arrow-right text-muted extra-small"></i>
                        </a>
                    `;
                });
            }

            // 4. Categories & Tags
            if ((data.categories && data.categories.length > 0) || (data.tags && data.tags.length > 0)) {
                html += `<div class="admin-search-group-title"><i class="bi bi-tags text-warning me-1"></i>Taxonomies &amp; Topics</div>`;
                if (data.categories) {
                    data.categories.forEach(c => {
                        html += `
                            <a href="${c.url}" class="admin-search-item">
                                <div class="admin-search-item-icon bg-warning bg-opacity-10 text-warning">
                                    <i class="bi bi-folder2"></i>
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fw-semibold small text-body text-truncate">${escapeHtml(c.name)}</div>
                                    <span class="extra-small text-muted">Category Desk</span>
                                </div>
                                <i class="bi bi-arrow-right text-muted extra-small"></i>
                            </a>
                        `;
                    });
                }
                if (data.tags) {
                    data.tags.forEach(t => {
                        html += `
                            <a href="${t.url}" class="admin-search-item">
                                <div class="admin-search-item-icon bg-secondary bg-opacity-10 text-secondary">
                                    <i class="bi bi-hash"></i>
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fw-semibold small text-body text-truncate">${escapeHtml(t.name)}</div>
                                    <span class="extra-small text-muted">Tag Keyword</span>
                                </div>
                                <i class="bi bi-arrow-right text-muted extra-small"></i>
                            </a>
                        `;
                    });
                }
            }

            // 5. Contact Inquiries
            if (data.contacts && data.contacts.length > 0) {
                html += `<div class="admin-search-group-title"><i class="bi bi-envelope text-danger me-1"></i>Patron Inquiries</div>`;
                data.contacts.forEach(c => {
                    html += `
                        <a href="${c.url}" class="admin-search-item">
                            <div class="admin-search-item-icon bg-danger bg-opacity-10 text-danger">
                                <i class="bi bi-chat-left-dots"></i>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="fw-semibold small text-body text-truncate">${escapeHtml(c.name)}</div>
                                <span class="extra-small text-muted text-truncate d-block">${escapeHtml(c.subject)}</span>
                            </div>
                            <i class="bi bi-box-arrow-up-right text-muted extra-small"></i>
                        </a>
                    `;
                });
            }

            // 6. Comments
            if (data.comments && data.comments.length > 0) {
                html += `<div class="admin-search-group-title"><i class="bi bi-chat-quote text-success me-1"></i>Reader Discussions</div>`;
                data.comments.forEach(c => {
                    html += `
                        <a href="${c.url}" class="admin-search-item">
                            <div class="admin-search-item-icon bg-success bg-opacity-10 text-success">
                                <i class="bi bi-chat-quote"></i>
                            </div>
                            <div class="flex-grow-1 overflow-hidden">
                                <div class="fw-semibold small text-body text-truncate">${escapeHtml(c.author)}</div>
                                <span class="extra-small text-muted text-truncate d-block">${escapeHtml(c.snippet)}</span>
                            </div>
                            <i class="bi bi-arrow-right text-muted extra-small"></i>
                        </a>
                    `;
                });
            }

            searchContent.innerHTML = html;
            currentItems = Array.from(searchContent.querySelectorAll('.admin-search-item'));
            activeIndex = -1;
        }

        // Global Keyboard Shortcut: Ctrl+K or Cmd+K
        window.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === 'K')) {
                e.preventDefault();
                searchInput.focus();
                searchInput.select();
                fetchLiveResults(searchInput.value.trim());
            }
        });

        // Search Input Events
        searchInput.addEventListener('input', function() {
            updateClearBtn();
            clearTimeout(debounceTimer);
            const val = this.value.trim();
            debounceTimer = setTimeout(() => {
                fetchLiveResults(val);
            }, 220);
        });

        searchInput.addEventListener('focus', function() {
            updateClearBtn();
            fetchLiveResults(this.value.trim());
        });

        searchInput.addEventListener('keydown', function(e) {
            if (searchDropdown.classList.contains('d-none')) {
                if (e.key === 'ArrowDown') {
                    showDropdown();
                }
                return;
            }

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (currentItems.length === 0) return;
                activeIndex = (activeIndex + 1) % currentItems.length;
                updateActiveState();
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (currentItems.length === 0) return;
                activeIndex = (activeIndex - 1 + currentItems.length) % currentItems.length;
                updateActiveState();
            } else if (e.key === 'Escape') {
                e.preventDefault();
                hideDropdown();
                searchInput.blur();
            } else if (e.key === 'Enter') {
                if (activeIndex >= 0 && currentItems[activeIndex]) {
                    e.preventDefault();
                    window.location.href = currentItems[activeIndex].href;
                }
            }
        });

        // Clear Search Button
        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                searchInput.value = '';
                updateClearBtn();
                hideDropdown();
                searchInput.focus();
            });
        }

        // Click Outside to Dismiss
        document.addEventListener('click', function(e) {
            if (!searchContainer.contains(e.target)) {
                hideDropdown();
            }
        });
    });

    // ========================================================
    // UNIVERSAL TABLE DROPDOWN ELEVATION (FIXES THREE-DOT CLIPPING)
    // ========================================================
    (function initTableDropdowns() {
        function configureDropdown(toggle) {
            if (!toggle || toggle.dataset.popperFixedInit) return;
            toggle.dataset.popperFixedInit = 'true';

            // Remove any legacy static display attribute that blocks Popper
            if (toggle.getAttribute('data-bs-display') === 'static') {
                toggle.removeAttribute('data-bs-display');
            }

            // Set boundary to viewport to prevent clipping by parent scrollbars
            toggle.setAttribute('data-bs-boundary', 'viewport');

            try {
                const existing = bootstrap.Dropdown.getInstance(toggle);
                if (existing) {
                    existing.dispose();
                }
                new bootstrap.Dropdown(toggle, {
                    boundary: 'viewport',
                    popperConfig: function(defaultBsPopperConfig) {
                        return {
                            ...defaultBsPopperConfig,
                            strategy: 'fixed'
                        };
                    }
                });
            } catch (e) {
                // Ignore initialization race conditions
            }
        }

        // Attach on DOM ready for all table action dropdowns
        document.addEventListener('DOMContentLoaded', function() {
            const selector = [
                '.table-responsive [data-bs-toggle="dropdown"]',
                'table [data-bs-toggle="dropdown"]',
                '.has-table [data-bs-toggle="dropdown"]',
                '.elite-datatable [data-bs-toggle="dropdown"]'
            ].join(',');

            document.querySelectorAll(selector).forEach(configureDropdown);
        });

        // Dynamic delegate: Catch dynamically rendered or late-loaded dropdowns
        document.addEventListener('show.bs.dropdown', function(e) {
            const toggle = e.target.closest('.table-responsive [data-bs-toggle="dropdown"], table [data-bs-toggle="dropdown"], .has-table [data-bs-toggle="dropdown"], .elite-datatable [data-bs-toggle="dropdown"]');
            if (toggle) {
                configureDropdown(toggle);
            }
        }, { capture: true });
    })();
</script>

@stack('scripts')
