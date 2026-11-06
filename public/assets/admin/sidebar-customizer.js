/**
 * Antigravity Admin Console - Sidebar Customizer Runtime
 * Matches user's uploaded image:
 * - Slide-in panel docked next to sidebar
 * - Drag & drop reordering (and Up/Down arrows)
 * - Eye icon to toggle hide/show (Dashboard and Settings locked)
 * - "Reset to default" and "✓ Done" buttons
 * - LocalStorage persistence across all pages and sessions
 */
(function ($, window, document) {
    'use strict';

    if (!window.AdminSidebarCustomizer) {
        window.AdminSidebarCustomizer = {};
    }

    var ASC = window.AdminSidebarCustomizer;
    var STORAGE_KEY = 'admin_sidebar_customization';
    var defaultItems = []; // [{ key: 'admin.dashboard', label: 'Dashboard', icon: 'squares-four', href: '...', locked: true, groupKey: 'overview' }]

    // Scan default items from sidebar
    function scanDefaultSidebarItems() {
        defaultItems = [];
        $('#sidebarNav .sb-item').each(function () {
            var $item = $(this);
            var $group = $item.closest('.sb-group');
            var key = $item.attr('data-nav-key') || $item.attr('href');
            var label = $item.attr('data-nav-label') || $item.find('.sb-label').text().trim();
            var icon = $item.attr('data-nav-icon') || ($item.find('.sb-icon i').attr('class') || '').replace(/^.*ph-([a-z0-9-]+).*$/, '$1');
            var isLocked = $item.attr('data-locked') === '1' || /dashboard|setting/i.test(key) || /dashboard|setting/i.test(label);

            if (key) {
                defaultItems.push({
                    key: key,
                    label: label,
                    icon: icon || 'squares-four',
                    href: $item.attr('href'),
                    locked: isLocked,
                    groupKey: $group.attr('data-group') || 'main'
                });
            }
        });
    }

    // Injects the customizer panel HTML into body
    function ensurePanelInjected() {
        if (document.getElementById('sbCustomizerBackdrop')) return;

        var html = 
            '<div id="sbCustomizerBackdrop" class="sb-customizer-backdrop is-hidden" aria-hidden="true">' +
            '    <div class="sb-customizer-panel" role="dialog" aria-modal="true" aria-labelledby="sbCustHeading">' +
            '        <!-- Circular Close Button (top-right) -->' +
            '        <button type="button" class="sb-cust-close-btn" id="sbCustCloseBtn" aria-label="Close">' +
            '            <i class="ph ph-x"></i>' +
            '        </button>' +
            '        <!-- Header Hint -->' +
            '        <div class="sb-cust-header">' +
            '            <p class="sb-cust-hint" id="sbCustHeading">' +
            '                Drag or use the arrows to reorder.<br>' +
            '                Use the eye to hide a menu.<br>' +
            '                <span class="sb-cust-hint-sub">Dashboard and Settings are always shown. Changes are saved to your account.</span>' +
            '            </p>' +
            '        </div>' +
            '        <!-- Items List -->' +
            '        <div class="sb-cust-list" id="sbCustList">' +
            '            <!-- Dynamically populated -->' +
            '        </div>' +
            '        <!-- Footer Actions -->' +
            '        <div class="sb-cust-footer">' +
            '            <button type="button" class="sb-cust-btn-reset" id="sbCustResetBtn">' +
            '                <i class="ph ph-arrow-counter-clockwise"></i> Reset to default' +
            '            </button>' +
            '            <button type="button" class="sb-cust-btn-done" id="sbCustDoneBtn">' +
            '                <i class="ph-bold ph-check"></i> Done' +
            '            </button>' +
            '        </div>' +
            '    </div>' +
            '</div>';

        $('body').append(html);
        bindPanelEvents();
    }

    // Bind interaction events for the panel
    function bindPanelEvents() {
        var $backdrop = $('#sbCustomizerBackdrop');
        var $list = $('#sbCustList');

        // Close handlers
        $('#sbCustCloseBtn').on('click', function () {
            ASC.close();
        });

        $backdrop.on('click', function (e) {
            if (e.target === this) ASC.close();
        });

        $(document).on('keydown', function (e) {
            if (e.key === 'Escape' && !$backdrop.hasClass('is-hidden')) {
                ASC.close();
            }
        });

        // Up arrow click
        $list.on('click', '.btn-up', function () {
            var $card = $(this).closest('.sb-cust-item');
            var $prev = $card.prev('.sb-cust-item');
            if ($prev.length) {
                $card.insertBefore($prev);
                updateArrowStates();
            }
        });

        // Down arrow click
        $list.on('click', '.btn-down', function () {
            var $card = $(this).closest('.sb-cust-item');
            var $next = $card.next('.sb-cust-item');
            if ($next.length) {
                $card.insertAfter($next);
                updateArrowStates();
            }
        });

        // Eye toggle click
        $list.on('click', '.sb-cust-btn-eye', function () {
            var $card = $(this).closest('.sb-cust-item');
            var isHidden = $card.hasClass('is-hidden-menu');

            if (isHidden) {
                $card.removeClass('is-hidden-menu');
                $(this).removeClass('is-hidden-state');
                $(this).find('i').attr('class', 'ph ph-eye');
            } else {
                $card.addClass('is-hidden-menu');
                $(this).addClass('is-hidden-state');
                $(this).find('i').attr('class', 'ph ph-eye-slash');
            }
        });

        // Real-time Smooth Drag and Drop
        var draggedItem = null;

        $list.on('dragstart', '.sb-cust-item', function (e) {
            draggedItem = this;
            if (e.originalEvent && e.originalEvent.dataTransfer) {
                e.originalEvent.dataTransfer.effectAllowed = 'move';
                e.originalEvent.dataTransfer.setData('text/plain', $(this).attr('data-key'));
            }
            setTimeout(function () {
                if (draggedItem) $(draggedItem).addClass('is-dragging');
            }, 0);
        });

        $list.on('dragover', function (e) {
            e.preventDefault();
            if (!draggedItem) return;
            if (e.originalEvent && e.originalEvent.dataTransfer) {
                e.originalEvent.dataTransfer.dropEffect = 'move';
            }

            var clientY = e.originalEvent ? e.originalEvent.clientY : e.clientY;
            var afterElement = getDragAfterElement(this, clientY);

            if (!afterElement) {
                this.appendChild(draggedItem);
            } else if (afterElement !== draggedItem) {
                this.insertBefore(draggedItem, afterElement);
            }
            updateArrowStates();
        });

        $list.on('dragend', '.sb-cust-item', function () {
            if (draggedItem) {
                $(draggedItem).removeClass('is-dragging');
                draggedItem = null;
            }
            updateArrowStates();
        });

        // Reset to default
        $('#sbCustResetBtn').on('click', function () {
            ASC.reset();
        });

        // Done button
        $('#sbCustDoneBtn').on('click', function () {
            ASC.save();
        });
    }

    function getDragAfterElement(container, y) {
        var elements = container.querySelectorAll('.sb-cust-item:not(.is-dragging)');
        var closest = { offset: Number.NEGATIVE_INFINITY, element: null };

        for (var i = 0; i < elements.length; i++) {
            var box = elements[i].getBoundingClientRect();
            var offset = y - box.top - box.height / 2;
            if (offset < 0 && offset > closest.offset) {
                closest = { offset: offset, element: elements[i] };
            }
        }
        return closest.element;
    }

    function updateArrowStates() {
        var $cards = $('#sbCustList .sb-cust-item');
        $cards.each(function (idx) {
            var $up = $(this).find('.btn-up');
            var $down = $(this).find('.btn-down');
            $up.prop('disabled', idx === 0);
            $down.prop('disabled', idx === $cards.length - 1);
        });
    }

    function renderItemCard(item, isHidden) {
        var iconHtml = '<i class="ph-duotone ph-' + item.icon + '"></i>';
        var actionHtml = '';

        if (item.locked) {
            actionHtml = '<span class="sb-cust-lock" title="Always shown"><i class="ph-fill ph-lock-simple"></i></span>';
        } else {
            actionHtml = 
                '<button type="button" class="sb-cust-btn-eye ' + (isHidden ? 'is-hidden-state' : '') + '" title="Toggle visibility">' +
                '    <i class="ph ' + (isHidden ? 'ph-eye-slash' : 'ph-eye') + '"></i>' +
                '</button>';
        }

        return (
            '<div class="sb-cust-item ' + (isHidden ? 'is-hidden-menu' : '') + '" data-key="' + item.key + '" draggable="true">' +
            '    <span class="sb-cust-drag-handle" title="Drag to reorder">' +
            '        <i class="ph ph-dots-six-vertical"></i>' +
            '    </span>' +
            '    <span class="sb-cust-icon">' + iconHtml + '</span>' +
            '    <span class="sb-cust-title">' + $('<div>').text(item.label).html() + '</span>' +
            '    <button type="button" class="sb-cust-btn-arrow btn-up" title="Move up"><i class="ph ph-caret-up"></i></button>' +
            '    <button type="button" class="sb-cust-btn-arrow btn-down" title="Move down"><i class="ph ph-caret-down"></i></button>' +
            '    ' + actionHtml +
            '</div>'
        );
    }

    // Public API
    ASC.open = function () {
        ensurePanelInjected();
        scanDefaultSidebarItems();

        var saved = ASC.loadState();
        var savedOrder = saved ? saved.order : [];
        var savedHidden = saved ? (saved.hidden || []) : [];

        // Build list in saved order or default order
        var orderedItems = [];
        var usedKeys = {};

        if (savedOrder && savedOrder.length) {
            savedOrder.forEach(function (key) {
                var item = defaultItems.find(function (d) { return d.key === key; });
                if (item) {
                    orderedItems.push(item);
                    usedKeys[key] = true;
                }
            });
        }

        // Add any remaining items that weren't in saved order
        defaultItems.forEach(function (d) {
            if (!usedKeys[d.key]) {
                orderedItems.push(d);
            }
        });

        // Render Cards
        var $list = $('#sbCustList').empty();
        orderedItems.forEach(function (item) {
            var isHidden = !item.locked && savedHidden.indexOf(item.key) !== -1;
            $list.append(renderItemCard(item, isHidden));
        });

        updateArrowStates();

        $('#sbCustomizerBackdrop').removeClass('is-hidden');
    };

    ASC.close = function () {
        $('#sbCustomizerBackdrop').addClass('is-hidden');
    };

    ASC.save = function () {
        var order = [];
        var hidden = [];

        $('#sbCustList .sb-cust-item').each(function () {
            var key = $(this).attr('data-key');
            order.push(key);
            if ($(this).hasClass('is-hidden-menu')) {
                hidden.push(key);
            }
        });

        var state = { order: order, hidden: hidden };
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
        } catch (e) {}

        ASC.applyToSidebar(state);
        ASC.close();

        if (window.AdminToast && window.AdminToast.show) {
            window.AdminToast.show('success', 'Sidebar menu customized successfully.');
        } else if (window.toastr && window.toastr.success) {
            window.toastr.success('Sidebar menu customized successfully.');
        }
    };

    ASC.reset = function () {
        try {
            localStorage.removeItem(STORAGE_KEY);
        } catch (e) {}

        scanDefaultSidebarItems();
        var $list = $('#sbCustList').empty();
        defaultItems.forEach(function (item) {
            $list.append(renderItemCard(item, false));
        });
        updateArrowStates();

        ASC.applyToSidebar({ order: defaultItems.map(function (d) { return d.key; }), hidden: [] });

        if (window.AdminToast && window.AdminToast.show) {
            window.AdminToast.show('info', 'Sidebar menu reset to default.');
        }
    };

    ASC.loadState = function () {
        try {
            var raw = localStorage.getItem(STORAGE_KEY);
            return raw ? JSON.parse(raw) : null;
        } catch (e) {
            return null;
        }
    };

    // Rearranges and filters the actual sidebar navigation DOM based on state
    ASC.applyToSidebar = function (state) {
        if (!state) state = ASC.loadState();
        if (!state) return;

        var order = state.order || [];
        var hidden = state.hidden || [];

        // 1. Reorder items:
        // We find the elements for each key in order and place them in sequence
        var $nav = $('#sidebarNav');
        if (!$nav.length) return;

        // If order is specified, reposition list items inside their groups or flatten
        order.forEach(function (key) {
            var $link = $nav.find('.sb-item[data-nav-key="' + key + '"]');
            if ($link.length) {
                var $li = $link.closest('li');
                var $ul = $li.closest('ul.sb-group-items');
                if ($ul.length) {
                    $ul.append($li); // Appends in order within its group
                }
            }
        });

        // 2. Hide items that are in hidden list (Dashboard & Settings can never be hidden)
        $nav.find('.sb-item').each(function () {
            var key = $(this).attr('data-nav-key') || $(this).attr('href');
            var isLocked = $(this).attr('data-locked') === '1' || /dashboard|setting/i.test(key);
            var shouldHide = !isLocked && hidden.indexOf(key) !== -1;
            var $li = $(this).closest('li');

            if (shouldHide) {
                $li.hide();
            } else {
                $li.show();
            }
        });

        // 3. If an entire group's items are hidden, hide the group
        $nav.find('.sb-group').each(function () {
            var visibleItems = $(this).find('ul.sb-group-items li').filter(function () {
                return $(this).css('display') !== 'none';
            });
            $(this).toggle(visibleItems.length > 0);
        });
    };

    // Auto-init on page load
    $(function () {
        scanDefaultSidebarItems();
        ASC.applyToSidebar();

        // Bind trigger button in sidebar
        $(document).on('click', '#openSidebarCustomizerBtn, [data-open-sidebar-customizer]', function (e) {
            e.preventDefault();
            ASC.open();
        });
    });

})(jQuery, window, document);

