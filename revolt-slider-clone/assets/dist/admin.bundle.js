(function () {
    const { createElement, render } = window.wp.element;

    const AppShell = (props) => {
        const { view, data } = props;

        const heading = {
            modules: 'Revolt Modules',
            templates: 'Templates Library',
            addons: 'Add-ons',
            settings: 'Global Settings',
        }[view] || 'Revolt Modules';

        const description = {
            modules: 'Create, manage, and customize slider modules.',
            templates: 'Start from pre-built templates tailored for niches.',
            addons: 'Extend functionality with additional effects and layers.',
            settings: 'Configure performance, defaults, and permissions.',
        }[view] || '';

        return createElement(
            'div',
            { className: 'revolt-admin-shell' },
            createElement('header', { className: 'revolt-admin-shell__header' },
                createElement('h1', null, heading),
                description && createElement('p', null, description)
            ),
            createElement('pre', { className: 'revolt-admin-shell__data' }, JSON.stringify(data, null, 2))
        );
    };

    const mount = function (selector, view) {
        const target = document.querySelector(selector);
        if (!target || typeof render !== 'function') {
            return;
        }

        const data = window.RevoltSliderData || {};
        render(createElement(AppShell, { view, data }), target);
    };

    document.addEventListener('DOMContentLoaded', function () {
        mount('#revolt-slider-app', 'modules');
        mount('#revolt-templates-app', 'templates');
        mount('#revolt-addons-app', 'addons');
        mount('#revolt-settings-app', 'settings');
    });
})();
