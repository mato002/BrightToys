/**
 * Admin Panel Enhancements
 * Implements essential admin panel features: global search, keyboard shortcuts,
 * bulk actions, loading states, session timeout, and accessibility improvements.
 */

(function() {
    'use strict';

    // ============================================
    // 1. GLOBAL SEARCH
    // ============================================
    const initGlobalSearch = () => {
        const searchInput = document.getElementById('global-search-input');
        const searchResults = document.getElementById('global-search-results');
        if (!searchInput || !searchResults) return;

        let searchTimeout;
        const searchModules = [
            { name: 'Products', route: 'admin.products.index', url: '/admin/products', icon: '📦' },
            { name: 'Orders', route: 'admin.orders.index', url: '/admin/orders', icon: '🛒' },
            { name: 'Users', route: 'admin.users.index', url: '/admin/users', icon: '👥' },
            { name: 'Partners', route: 'admin.partners.index', url: '/admin/partners', icon: '🤝' },
            { name: 'Members', route: 'admin.members.index', url: '/admin/members', icon: '👤' },
            { name: 'Projects', route: 'admin.projects.index', url: '/admin/projects', icon: '🏗️' },
            { name: 'Loans', route: 'admin.loans.index', url: '/admin/loans', icon: '💰' },
            { name: 'Documents', route: 'admin.documents.index', url: '/admin/documents', icon: '📄' },
        ];

        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();

            if (query.length < 2) {
                searchResults.classList.add('hidden');
                return;
            }

            searchTimeout = setTimeout(() => {
                // Simple client-side filtering (can be enhanced with AJAX)
                const filtered = searchModules.filter(m => 
                    m.name.toLowerCase().includes(query.toLowerCase())
                );

                if (filtered.length > 0) {
                    searchResults.innerHTML = filtered.map(m => `
                        <a href="${m.url || '#'}?q=${encodeURIComponent(query)}" 
                           class="flex items-center gap-2 px-3 py-2 hover:bg-slate-100 rounded transition">
                            <span>${m.icon}</span>
                            <span class="text-sm">Search "${query}" in ${m.name}</span>
                        </a>
                    `).join('');
                    searchResults.classList.remove('hidden');
                } else {
                    searchResults.innerHTML = `
                        <div class="px-3 py-4 text-center text-sm text-slate-500">
                            No matching modules found
                        </div>
                    `;
                    searchResults.classList.remove('hidden');
                }
            }, 200);
        });

        // Close on outside click
        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.classList.add('hidden');
            }
        });
    };

    // ============================================
    // 2. KEYBOARD SHORTCUTS
    // ============================================
    const initKeyboardShortcuts = () => {
        let isSearchFocused = false;

        // Focus search with "/"
        document.addEventListener('keydown', (e) => {
            // Don't trigger if typing in input/textarea
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
                if (e.key === '/') {
                    e.preventDefault();
                    const searchInput = document.getElementById('global-search-input');
                    if (searchInput) {
                        searchInput.focus();
                        searchInput.select();
                    }
                }
                return;
            }

            // Global shortcuts
            switch(e.key) {
                case '/':
                    e.preventDefault();
                    const searchInput = document.getElementById('global-search-input');
                    if (searchInput) {
                        searchInput.focus();
                        searchInput.select();
                    }
                    break;
                case 'Escape':
                    // Close modals, dropdowns
                    document.querySelectorAll('.modal, [role="dialog"]').forEach(el => {
                        if (!el.classList.contains('hidden')) {
                            el.classList.add('hidden');
                        }
                    });
                    break;
            }

            // Ctrl/Cmd + S to save forms
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                const form = document.querySelector('form:not([method="get"])');
                if (form) {
                    const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                    if (submitBtn && !submitBtn.disabled) {
                        submitBtn.click();
                    }
                }
            }
        });
    };

    // ============================================
    // 3. BULK ACTIONS
    // ============================================
    const initBulkActions = () => {
        const selectAllCheckbox = document.getElementById('select-all');
        const itemCheckboxes = document.querySelectorAll('.item-checkbox');
        const bulkActionsBar = document.getElementById('bulk-actions-bar');
        const bulkActionForm = document.getElementById('bulk-action-form');

        if (!selectAllCheckbox || !bulkActionsBar) return;

        const updateBulkActions = () => {
            const checked = document.querySelectorAll('.item-checkbox:checked');
            if (checked.length > 0) {
                bulkActionsBar.classList.remove('hidden');
                document.getElementById('selected-count').textContent = checked.length;
            } else {
                bulkActionsBar.classList.add('hidden');
            }
        };

        // Select all
        selectAllCheckbox.addEventListener('change', (e) => {
            itemCheckboxes.forEach(cb => {
                cb.checked = e.target.checked;
            });
            updateBulkActions();
        });

        // Individual checkboxes
        itemCheckboxes.forEach(cb => {
            cb.addEventListener('change', updateBulkActions);
        });

        // Bulk delete
        const bulkDeleteBtn = document.getElementById('bulk-delete');
        if (bulkDeleteBtn) {
            bulkDeleteBtn.addEventListener('click', (e) => {
                e.preventDefault();
                const checked = Array.from(document.querySelectorAll('.item-checkbox:checked'))
                    .map(cb => cb.value);

                if (checked.length === 0) return;

                Swal.fire({
                    title: 'Delete Selected Items?',
                    text: `Are you sure you want to delete ${checked.length} item(s)? This cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, delete them',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed && bulkActionForm) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'ids';
                        input.value = JSON.stringify(checked);
                        bulkActionForm.appendChild(input);
                        bulkActionForm.submit();
                    }
                });
            });
        }
    };

    // ============================================
    // 4. LOADING STATES
    // ============================================
    const initLoadingStates = () => {
        // Show loading on form submissions
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
                if (submitBtn && !submitBtn.dataset.noLoading) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="inline-block animate-spin mr-2">⏳</span> Processing...';
                }
            });
        });

        // Show loading on links with data-loading attribute
        document.querySelectorAll('a[data-loading]').forEach(link => {
            link.addEventListener('click', function() {
                this.innerHTML = '<span class="inline-block animate-spin mr-2">⏳</span> Loading...';
                this.style.pointerEvents = 'none';
            });
        });
    };

    // ============================================
    // 5. SESSION TIMEOUT WARNING
    // ============================================
    const initSessionTimeout = () => {
        // Session lifetime in minutes (default 120), converted to milliseconds
        const sessionLifetimeMinutes = window.sessionLifetimeMinutes || 120;
        const sessionLifetime = sessionLifetimeMinutes * 60 * 1000;
        const warningTime = 5 * 60 * 1000; // Warn 5 minutes before expiry

        let warningShown = false;

        const checkSession = () => {
            const timeLeft = sessionLifetime - (Date.now() - (window.sessionStartTime || Date.now()));
            
            if (timeLeft < warningTime && !warningShown) {
                warningShown = true;
                const minutesLeft = Math.round(timeLeft / 60000);
                Swal.fire({
                    title: 'Session Expiring Soon',
                    html: `Your session will expire in ${minutesLeft} minute${minutesLeft !== 1 ? 's' : ''}. Would you like to extend it?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Extend Session',
                    cancelButtonText: 'Logout Now',
                    timer: timeLeft,
                    timerProgressBar: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Ping server to extend session
                        const dashboardUrl = window.dashboardUrl || '/admin';
                        fetch(dashboardUrl, { method: 'HEAD' })
                            .then(() => {
                                Swal.fire('Session Extended', 'Your session has been extended.', 'success');
                                warningShown = false;
                                window.sessionStartTime = Date.now(); // Reset timer
                            })
                            .catch(() => {
                                Swal.fire('Error', 'Could not extend session. Please refresh the page.', 'error');
                            });
                    } else if (result.dismiss === Swal.DismissReason.timer) {
                        const logoutUrl = window.logoutUrl || '/logout';
                        window.location.href = logoutUrl;
                    }
                });
            }
        };

        // Check every minute
        setInterval(checkSession, 60000);
    };

    // Store session start time
    window.sessionStartTime = Date.now();

    // ============================================
    // 6. TABLE SORTING INDICATORS
    // ============================================
    const initTableSorting = () => {
        document.querySelectorAll('th[data-sortable]').forEach(th => {
            th.style.cursor = 'pointer';
            th.addEventListener('click', function() {
                const column = this.dataset.column;
                const currentSort = new URLSearchParams(window.location.search).get('sort');
                const currentOrder = new URLSearchParams(window.location.search).get('order');
                
                let newOrder = 'asc';
                if (currentSort === column && currentOrder === 'asc') {
                    newOrder = 'desc';
                }

                const url = new URL(window.location);
                url.searchParams.set('sort', column);
                url.searchParams.set('order', newOrder);
                window.location.href = url.toString();
            });

            // Add visual indicator
            const currentSort = new URLSearchParams(window.location.search).get('sort');
            const currentOrder = new URLSearchParams(window.location.search).get('order');
            if (currentSort === th.dataset.column) {
                const indicator = document.createElement('span');
                indicator.className = 'ml-1 text-emerald-600';
                indicator.textContent = currentOrder === 'asc' ? '↑' : '↓';
                th.appendChild(indicator);
            }
        });
    };

    // ============================================
    // 7. ACCESSIBILITY IMPROVEMENTS
    // ============================================
    const initAccessibility = () => {
        // Add ARIA labels to icon-only buttons
        document.querySelectorAll('button:not([aria-label]):not([aria-labelledby])').forEach(btn => {
            if (btn.textContent.trim() === '' && btn.querySelector('svg')) {
                const title = btn.getAttribute('title') || btn.getAttribute('data-tooltip') || 'Button';
                btn.setAttribute('aria-label', title);
            }
        });

        // Improve focus visibility
        const style = document.createElement('style');
        style.textContent = `
            *:focus-visible {
                outline: 2px solid #10b981;
                outline-offset: 2px;
            }
            button:focus-visible, a:focus-visible {
                outline: 2px solid #10b981;
                outline-offset: 2px;
            }
        `;
        document.head.appendChild(style);

        // Skip to main content link
        const skipLink = document.createElement('a');
        skipLink.href = '#main-content';
        skipLink.textContent = 'Skip to main content';
        skipLink.className = 'sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:z-50 focus:px-4 focus:py-2 focus:bg-emerald-600 focus:text-white focus:rounded';
        document.body.insertBefore(skipLink, document.body.firstChild);
    };

    // ============================================
    // 8. BREADCRUMBS
    // ============================================
    const initBreadcrumbs = () => {
        // Breadcrumbs are rendered server-side, but we can enhance them
        const breadcrumbs = document.querySelector('.breadcrumbs');
        if (breadcrumbs) {
            breadcrumbs.setAttribute('aria-label', 'Breadcrumb navigation');
        }
    };

    // ============================================
    // 7. RESPONSIVE TABLES
    // ============================================
    const initResponsiveTables = () => {
        // Find all tables and make them responsive on mobile
        const tables = document.querySelectorAll('table');
        
        tables.forEach(table => {
            // Add responsive-table class if not already present
            if (!table.classList.contains('responsive-table')) {
                table.classList.add('responsive-table');
            }

            // Add data-label attributes to td elements based on th headers
            const thead = table.querySelector('thead');
            const tbody = table.querySelector('tbody');
            
            if (thead && tbody) {
                const headers = Array.from(thead.querySelectorAll('th')).map(th => {
                    // Get text content, excluding checkbox and action columns
                    const text = th.textContent.trim();
                    // Skip if it's a checkbox column or empty
                    if (text === '' || th.querySelector('input[type="checkbox"]')) {
                        return null;
                    }
                    return text;
                });

                tbody.querySelectorAll('tr').forEach(row => {
                    const cells = Array.from(row.querySelectorAll('td'));
                    cells.forEach((cell, index) => {
                        if (headers[index] && !cell.hasAttribute('data-label')) {
                            cell.setAttribute('data-label', headers[index]);
                        }
                    });
                });
            }
        });
    };

    // ============================================
    // 8. MOBILE MENU TOGGLE
    // ============================================
    const initMobileMenu = () => {
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const mobileSidebarClose = document.getElementById('mobile-sidebar-close');
        const mobileBackdrop = document.getElementById('mobile-sidebar-backdrop');
        const body = document.body;

        const openSidebar = () => {
            body.classList.add('mobile-sidebar-open');
            if (mobileBackdrop) {
                mobileBackdrop.classList.remove('opacity-0', 'pointer-events-none');
                mobileBackdrop.classList.add('opacity-100');
            }
        };

        const closeSidebar = () => {
            body.classList.remove('mobile-sidebar-open');
            if (mobileBackdrop) {
                mobileBackdrop.classList.add('opacity-0', 'pointer-events-none');
                mobileBackdrop.classList.remove('opacity-100');
            }
        };

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', openSidebar);
        }

        if (mobileSidebarClose) {
            mobileSidebarClose.addEventListener('click', closeSidebar);
        }

        if (mobileBackdrop) {
            mobileBackdrop.addEventListener('click', closeSidebar);
        }

        // Close sidebar when clicking on a link (mobile only)
        const sidebarLinks = document.querySelectorAll('#admin-sidebar a');
        sidebarLinks.forEach(link => {
            link.addEventListener('click', () => {
                if (window.innerWidth < 1024) {
                    closeSidebar();
                }
            });
        });

        // Close sidebar on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && body.classList.contains('mobile-sidebar-open')) {
                closeSidebar();
            }
        });
    };

    // ============================================
    // INITIALIZE ALL FEATURES
    // ============================================
    document.addEventListener('DOMContentLoaded', () => {
        initGlobalSearch();
        initKeyboardShortcuts();
        initBulkActions();
        initLoadingStates();
        initSessionTimeout();
        initTableSorting();
        initAccessibility();
        initBreadcrumbs();
        initResponsiveTables();
        initMobileMenu();
    });

    // Export for use in other scripts
    window.AdminEnhancements = {
        showLoading: (element) => {
            if (element) {
                element.disabled = true;
                element.dataset.originalText = element.innerHTML;
                element.innerHTML = '<span class="inline-block animate-spin mr-2">⏳</span> Loading...';
            }
        },
        hideLoading: (element) => {
            if (element && element.dataset.originalText) {
                element.disabled = false;
                element.innerHTML = element.dataset.originalText;
                delete element.dataset.originalText;
            }
        }
    };
})();
