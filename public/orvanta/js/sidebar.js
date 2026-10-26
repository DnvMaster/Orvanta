document.addEventListener('DOMContentLoaded', function () {
    const crmLayout = document.getElementById('crmLayout');
    const sidebarToggle = document.getElementById('sidebarToggle');

    if (!crmLayout || !sidebarToggle) {
        return;
    }

    sidebarToggle.addEventListener('click', function () {
        const isCollapsed = crmLayout.classList.toggle('sidebar-collapsed');

        const label = isCollapsed
            ? 'Развернуть меню'
            : 'Свернуть меню';

        sidebarToggle.setAttribute('aria-label', label);
        sidebarToggle.setAttribute('title', label);
    });
});
