/**
 * Issue Detail & Triage Interaction Script
 * Supports both native <select name="action"> and legacy Bootstrap dropdown items if present.
 */
(function () {
    'use strict';

    function initIssueStatusForm() {
        var dropdownItems = document.querySelectorAll('.dropdown-item[name="action"], .dropdown-item[value]');
        dropdownItems.forEach(function (item) {
            item.addEventListener('click', function (e) {
                e.preventDefault();
                var action = item.getAttribute('value');
                var form = item.closest('form');
                if (!form || !action) return;

                if (action === 'rejected') {
                    if (!window.confirm('Are you sure you want to reject this issue? This action will mark the report as Rejected.')) {
                        return;
                    }
                }

                var existing = form.querySelector('input[name="action"]');
                if (existing) existing.remove();

                var hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'action';
                hiddenInput.value = action;
                form.appendChild(hiddenInput);
                form.submit();
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initIssueStatusForm);
    } else {
        initIssueStatusForm();
    }
})();
