/**
 * ReportEase Shared UI & Accessibility Controller
 * Handles:
 * 1. Collapsible mobile sidebar drawer (aria-expanded, Escape key, backdrop)
 * 2. Confirmation dialogs for destructive actions (reject issue, delete/remove)
 * 3. Double-submit prevention with loading spinner & aria-busy state
 */
(function () {
    'use strict';

    function initMobileDrawer() {
        var toggleBtn = document.querySelector('.mobile-nav-toggle');
        var sidebar = document.getElementById('dashboard-sidebar');
        var backdrop = document.getElementById('sidebar-backdrop');

        if (!toggleBtn || !sidebar) {
            return;
        }

        function setDrawerState(isOpen) {
            toggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            sidebar.classList.toggle('is-open', isOpen);
            if (backdrop) {
                backdrop.classList.toggle('is-visible', isOpen);
                backdrop.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
            }
            document.body.classList.toggle('drawer-open', isOpen);
        }

        toggleBtn.addEventListener('click', function () {
            var expanded = toggleBtn.getAttribute('aria-expanded') === 'true';
            setDrawerState(!expanded);
            if (!expanded) {
                var firstLink = sidebar.querySelector('a, button');
                if (firstLink) {
                    firstLink.focus();
                }
            }
        });

        if (backdrop) {
            backdrop.addEventListener('click', function () {
                setDrawerState(false);
                toggleBtn.focus();
            });
        }

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && toggleBtn.getAttribute('aria-expanded') === 'true') {
                setDrawerState(false);
                toggleBtn.focus();
            }
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth >= 768 && toggleBtn.getAttribute('aria-expanded') === 'true') {
                setDrawerState(false);
            }
        });
    }

    function initFormUX() {
        var forms = document.querySelectorAll('form');

        forms.forEach(function (form) {
            form.addEventListener('submit', function (event) {
                // Check for destructive confirmation requirement on form or clicked submitter
                var submitter = event.submitter || document.activeElement;
                var confirmMessage = form.getAttribute('data-confirm');

                if (!confirmMessage && submitter && submitter.getAttribute) {
                    confirmMessage = submitter.getAttribute('data-confirm');
                }

                // Also check if status select is set to a destructive action ('rejected')
                var actionSelect = form.querySelector('select[name="action"]');
                if (!confirmMessage && actionSelect && actionSelect.value === 'rejected') {
                    confirmMessage = 'Are you sure you want to reject this issue? This action will mark the report as Rejected.';
                }

                if (confirmMessage) {
                    if (!window.confirm(confirmMessage)) {
                        event.preventDefault();
                        return;
                    }
                }

                // Prevent double submission if already submitting
                if (form.getAttribute('data-submitting') === 'true') {
                    event.preventDefault();
                    return;
                }

                // Native HTML5 constraint validation check
                if (typeof form.checkValidity === 'function' && !form.checkValidity()) {
                    return;
                }

                form.setAttribute('data-submitting', 'true');
                form.setAttribute('aria-busy', 'true');

                var submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');
                submitButtons.forEach(function (btn) {
                    // Preserve clicked button's name/value if it carries form data
                    if (btn === submitter && btn.name && btn.value) {
                        var hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = btn.name;
                        hidden.value = btn.value;
                        form.appendChild(hidden);
                    }

                    var loadingText = btn.getAttribute('data-loading-text') || 'Submitting...';
                    btn.setAttribute('data-submitting', 'true');
                    btn.disabled = true;

                    if (btn.tagName === 'BUTTON') {
                        btn.innerHTML = '<span class="btn-spinner" aria-hidden="true"></span><span>' + loadingText + '</span>';
                    } else {
                        btn.value = loadingText;
                    }
                });
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initMobileDrawer();
            initFormUX();
        });
    } else {
        initMobileDrawer();
        initFormUX();
    }
})();
