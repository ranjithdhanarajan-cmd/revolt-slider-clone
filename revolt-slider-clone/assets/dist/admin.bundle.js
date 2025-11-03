(function () {
    const data = window.RevoltSliderData || {};
    const wp = window.wp || {};
    const element = wp.element || {};
    const createElement = element.createElement || function () {};
    const renderLegacy = element.render || wp.render;
    const createRoot = element.createRoot;
    const useState = element.useState || function () { return []; };
    const Fragment = element.Fragment || 'div';

    if (typeof createElement !== 'function') {
        return;
    }

    const apiFetch = wp.apiFetch;

    if (apiFetch && data.nonce && !apiFetch.__revoltNonceSet) {
        if (typeof apiFetch.createNonceMiddleware === 'function') {
            apiFetch.use(apiFetch.createNonceMiddleware(data.nonce));
            apiFetch.__revoltNonceSet = true;
        } else if (typeof apiFetch.setNonce === 'function') {
            apiFetch.setNonce(data.nonce);
            apiFetch.__revoltNonceSet = true;
        }
    }

    const __ = (wp.i18n && typeof wp.i18n.__ === 'function') ? wp.i18n.__ : function (text) { return text; };

    const ensureSettingsShape = function (settings) {
        const base = {
            performance: {
                lazyLoad: true,
                disableAnimationsMobile: false,
            },
            defaults: {
                width: 1200,
                height: 600,
                autoplay: true,
                delay: 5000,
                loop: true,
            },
            permissions: {
                canEditModules: 'edit_revolt_modules',
            },
        };

        const source = settings && typeof settings === 'object' ? settings : {};

        return {
            performance: Object.assign({}, base.performance, source.performance || {}),
            defaults: Object.assign({}, base.defaults, source.defaults || {}),
            permissions: Object.assign({}, base.permissions, source.permissions || {}),
        };
    };

    const formatDate = function (value) {
        if (!value) {
            return __('Never', 'revolt-slider-clone');
        }

        try {
            const date = new Date(value);
            if (!Number.isNaN(date.getTime())) {
                return date.toLocaleString();
            }
        } catch (e) {
            // Ignore parsing errors and fall back to raw value.
        }

        return value;
    };

    const request = function (options) {
        if (apiFetch && typeof apiFetch === 'function') {
            return apiFetch(options);
        }

        const restRoot = typeof data.restUrl === 'string' ? data.restUrl.replace(/\/$/, '') : '';
        const url = options.path ? (restRoot ? restRoot + options.path : options.path) : options.url;
        const fetchOptions = {
            method: options.method || 'GET',
            credentials: 'same-origin',
            headers: Object.assign({
                'Content-Type': 'application/json',
            }, options.headers || {}, data.nonce ? { 'X-WP-Nonce': data.nonce } : {}),
        };

        if (options.data) {
            fetchOptions.body = JSON.stringify(options.data);
        }

        return window.fetch(url, fetchOptions).then(function (response) {
            if (!response.ok) {
                return response.json().then(function (json) {
                    const message = json && json.message ? json.message : __('An unexpected error occurred.', 'revolt-slider-clone');
                    throw new Error(message);
                }).catch(function () {
                    throw new Error(__('An unexpected error occurred.', 'revolt-slider-clone'));
                });
            }

            return response.json();
        });
    };

    const Header = function (props) {
        const headingMap = {
            modules: __('Revolt Modules', 'revolt-slider-clone'),
            templates: __('Templates Library', 'revolt-slider-clone'),
            addons: __('Add-ons', 'revolt-slider-clone'),
            settings: __('Global Settings', 'revolt-slider-clone'),
        };

        const descriptionMap = {
            modules: __('Create, manage, and customize slider modules.', 'revolt-slider-clone'),
            templates: __('Start from pre-built compositions tailored for niches.', 'revolt-slider-clone'),
            addons: __('Extend functionality with additional effects and layer types.', 'revolt-slider-clone'),
            settings: __('Configure performance, defaults, and permissions.', 'revolt-slider-clone'),
        };

        return createElement('header', { className: 'revolt-admin-shell__header' },
            createElement('div', null,
                createElement('h1', null, headingMap[props.view] || __('Revolt Modules', 'revolt-slider-clone')),
                createElement('p', null, descriptionMap[props.view] || '')
            )
        );
    };

    const ModulesView = function (props) {
        const modules = Array.isArray(props.modules) ? props.modules : [];

        if (!modules.length) {
            return createElement('div', { className: 'revolt-empty-state' },
                createElement('p', null, props.i18n && props.i18n.noModules ? props.i18n.noModules : __('No modules yet. Create your first one!', 'revolt-slider-clone')),
                props.adminUrls && props.adminUrls.newModule ?
                    createElement('a', { className: 'button button-primary', href: props.adminUrls.newModule }, props.i18n && props.i18n.createModule ? props.i18n.createModule : __('Create Module', 'revolt-slider-clone')) : null
            );
        }

        return createElement(Fragment, null,
            createElement('div', { className: 'revolt-admin-shell__actions' },
                props.adminUrls && props.adminUrls.newModule ?
                    createElement('a', { className: 'button button-primary', href: props.adminUrls.newModule }, props.i18n && props.i18n.createModule ? props.i18n.createModule : __('Create Module', 'revolt-slider-clone')) : null,
                props.adminUrls && props.adminUrls.modulesList ?
                    createElement('a', { className: 'button', href: props.adminUrls.modulesList }, __('Open classic list view', 'revolt-slider-clone')) : null
            ),
            createElement('div', { className: 'revolt-card-grid' }, modules.map(function (module) {
                const editHref = props.adminUrls && props.adminUrls.editModule ? props.adminUrls.editModule + '?post=' + module.id + '&action=edit' : '#';
                const shortcode = module.alias ? '[revolt_slider alias="' + module.alias + '"]' : __('Alias not set', 'revolt-slider-clone');

                return createElement('section', { key: module.id, className: 'revolt-card' },
                    createElement('div', { className: 'revolt-card__header' },
                        createElement('h2', null, module.title || __('Untitled module', 'revolt-slider-clone')),
                        createElement('span', { className: 'revolt-card__badge' }, module.type || __('Unknown', 'revolt-slider-clone'))
                    ),
                    createElement('div', { className: 'revolt-card__body' },
                        createElement('p', { className: 'revolt-card__meta' },
                            createElement('strong', null, __('Alias:', 'revolt-slider-clone') + ' '),
                            shortcode
                        ),
                        createElement('p', { className: 'revolt-card__meta' },
                            createElement('strong', null, __('Last modified:', 'revolt-slider-clone') + ' '),
                            formatDate(module.modified)
                        )
                    ),
                    createElement('div', { className: 'revolt-card__footer' },
                        createElement('a', { className: 'button button-secondary', href: editHref }, __('Open module', 'revolt-slider-clone')),
                        props.adminUrls && props.adminUrls.modulesList ?
                            createElement('a', { className: 'button-link', href: props.adminUrls.modulesList + '&s=' + encodeURIComponent(module.title || '') }, __('View in list', 'revolt-slider-clone')) : null
                    )
                );
            }))
        );
    };

    const TemplatesView = function (props) {
        const templates = Array.isArray(props.templates) ? props.templates : [];

        if (!templates.length) {
            return createElement('div', { className: 'revolt-empty-state' },
                createElement('p', null, __('Templates will appear here once added to the library.', 'revolt-slider-clone'))
            );
        }

        return createElement('div', { className: 'revolt-card-grid' }, templates.map(function (template) {
            return createElement('section', { key: template.slug, className: 'revolt-card' },
                createElement('div', { className: 'revolt-card__media' },
                    template.preview ? createElement('img', { src: template.preview, alt: template.slug }) : createElement('div', { className: 'revolt-card__media--placeholder' }, __('Preview coming soon', 'revolt-slider-clone'))
                ),
                createElement('div', { className: 'revolt-card__body' },
                    createElement('h2', null, template.data && template.data.title ? template.data.title : template.slug),
                    template.data && template.data.type ? createElement('p', { className: 'revolt-card__meta' }, __('Type:', 'revolt-slider-clone') + ' ' + template.data.type) : null,
                    createElement('p', { className: 'revolt-card__meta' }, __('Slides:', 'revolt-slider-clone') + ' ' + (template.data && Array.isArray(template.data.slides) ? template.data.slides.length : 0))
                ),
                createElement('div', { className: 'revolt-card__footer' },
                    props.adminUrls && props.adminUrls.newModule ?
                        createElement('a', { className: 'button button-primary', href: props.adminUrls.newModule }, __('Use template', 'revolt-slider-clone')) : null
                )
            );
        }));
    };

    const AddonsView = function (props) {
        const addons = props.addons && typeof props.addons === 'object' ? props.addons : {};
        const slugs = Object.keys(addons);

        if (!slugs.length) {
            return createElement('div', { className: 'revolt-empty-state' },
                createElement('p', null, __('No add-ons registered yet. Developers can register add-ons via PHP hooks.', 'revolt-slider-clone'))
            );
        }

        return createElement('div', { className: 'revolt-card-grid' }, slugs.map(function (slug) {
            const addon = addons[slug] || {};

            return createElement('section', { key: slug, className: 'revolt-card' },
                createElement('div', { className: 'revolt-card__header' },
                    createElement('h2', null, addon.name || slug),
                    addon.version ? createElement('span', { className: 'revolt-card__badge' }, __('v', 'revolt-slider-clone') + addon.version) : null
                ),
                createElement('div', { className: 'revolt-card__body' },
                    addon.description ? createElement('p', null, addon.description) : null,
                    Array.isArray(addon.layers) && addon.layers.length ?
                        createElement('div', { className: 'revolt-card__meta' },
                            createElement('strong', null, __('Layer types:', 'revolt-slider-clone') + ' '),
                            addon.layers.map(function (layer) { return layer.label || layer.type; }).join(', ')
                        ) : null,
                    Array.isArray(addon.effects) && addon.effects.length ?
                        createElement('div', { className: 'revolt-card__meta' },
                            createElement('strong', null, __('Effects:', 'revolt-slider-clone') + ' '),
                            addon.effects.map(function (effect) { return effect.label || effect.type; }).join(', ')
                        ) : null
                ),
                createElement('div', { className: 'revolt-card__footer' },
                    addon.author ? createElement('span', { className: 'revolt-card__meta' }, __('By', 'revolt-slider-clone') + ' ' + addon.author) : null
                )
            );
        }));
    };

    const SettingsView = function (props) {
        const initial = ensureSettingsShape(props.settings);
        const [formState, setFormState] = useState(initial);
        const [saving, setSaving] = useState(false);
        const [notice, setNotice] = useState(null);

        const updateField = function (group, key, value) {
            setFormState(function (prev) {
                const next = ensureSettingsShape(prev);
                next[group][key] = value;
                return Object.assign({}, next);
            });
        };

        const resetToDefaults = function () {
            setFormState(ensureSettingsShape({}));
            setNotice(null);
        };

        const restRoot = typeof data.restUrl === 'string' ? data.restUrl.replace(/\/$/, '') : '';
        const endpoint = restRoot ? restRoot + '/settings' : '/wp-json/revolt/v1/settings';

        const onSubmit = function (event) {
            event.preventDefault();
            setSaving(true);
            setNotice(null);

            request({
                url: endpoint,
                method: 'POST',
                data: formState,
            }).then(function (response) {
                setFormState(ensureSettingsShape(response));
                setSaving(false);
                setNotice({ type: 'success', message: __('Settings saved successfully.', 'revolt-slider-clone') });
            }).catch(function (error) {
                setSaving(false);
                setNotice({ type: 'error', message: error && error.message ? error.message : __('Unable to save settings.', 'revolt-slider-clone') });
            });
        };

        return createElement('form', { className: 'revolt-settings-form', onSubmit: onSubmit },
            notice ? createElement('div', { className: 'notice ' + (notice.type === 'error' ? 'notice-error' : 'notice-success') },
                createElement('p', null, notice.message)
            ) : null,
            createElement('section', { className: 'revolt-settings-group' },
                createElement('h2', null, __('Performance', 'revolt-slider-clone')),
                createElement('label', { className: 'revolt-control' },
                    createElement('span', { className: 'revolt-control__label' }, __('Lazy load media', 'revolt-slider-clone')),
                    createElement('input', {
                        type: 'checkbox',
                        checked: !!formState.performance.lazyLoad,
                        onChange: function (event) { updateField('performance', 'lazyLoad', event.target.checked); },
                    }),
                    createElement('span', { className: 'revolt-control__help' }, __('Delay loading of images and videos until they are visible.', 'revolt-slider-clone'))
                ),
                createElement('label', { className: 'revolt-control' },
                    createElement('span', { className: 'revolt-control__label' }, __('Disable animations on mobile', 'revolt-slider-clone')),
                    createElement('input', {
                        type: 'checkbox',
                        checked: !!formState.performance.disableAnimationsMobile,
                        onChange: function (event) { updateField('performance', 'disableAnimationsMobile', event.target.checked); },
                    }),
                    createElement('span', { className: 'revolt-control__help' }, __('Improve performance on mobile devices by reducing animation effects.', 'revolt-slider-clone'))
                )
            ),
            createElement('section', { className: 'revolt-settings-group' },
                createElement('h2', null, __('Module defaults', 'revolt-slider-clone')),
                createElement('div', { className: 'revolt-control-row' },
                    createElement('label', { className: 'revolt-control' },
                        createElement('span', { className: 'revolt-control__label' }, __('Canvas width (px)', 'revolt-slider-clone')),
                        createElement('input', {
                            type: 'number',
                            min: 320,
                            value: formState.defaults.width,
                            onChange: function (event) { updateField('defaults', 'width', parseInt(event.target.value, 10) || 0); },
                        })
                    ),
                    createElement('label', { className: 'revolt-control' },
                        createElement('span', { className: 'revolt-control__label' }, __('Canvas height (px)', 'revolt-slider-clone')),
                        createElement('input', {
                            type: 'number',
                            min: 180,
                            value: formState.defaults.height,
                            onChange: function (event) { updateField('defaults', 'height', parseInt(event.target.value, 10) || 0); },
                        })
                    )
                ),
                createElement('label', { className: 'revolt-control' },
                    createElement('span', { className: 'revolt-control__label' }, __('Autoplay', 'revolt-slider-clone')),
                    createElement('input', {
                        type: 'checkbox',
                        checked: !!formState.defaults.autoplay,
                        onChange: function (event) { updateField('defaults', 'autoplay', event.target.checked); },
                    })
                ),
                createElement('label', { className: 'revolt-control' },
                    createElement('span', { className: 'revolt-control__label' }, __('Loop slides', 'revolt-slider-clone')),
                    createElement('input', {
                        type: 'checkbox',
                        checked: !!formState.defaults.loop,
                        onChange: function (event) { updateField('defaults', 'loop', event.target.checked); },
                    })
                ),
                createElement('label', { className: 'revolt-control' },
                    createElement('span', { className: 'revolt-control__label' }, __('Autoplay delay (ms)', 'revolt-slider-clone')),
                    createElement('input', {
                        type: 'number',
                        min: 0,
                        step: 100,
                        value: formState.defaults.delay,
                        onChange: function (event) { updateField('defaults', 'delay', parseInt(event.target.value, 10) || 0); },
                    })
                )
            ),
            createElement('section', { className: 'revolt-settings-group' },
                createElement('h2', null, __('Permissions', 'revolt-slider-clone')),
                createElement('label', { className: 'revolt-control' },
                    createElement('span', { className: 'revolt-control__label' }, __('Capability required to edit modules', 'revolt-slider-clone')),
                    createElement('input', {
                        type: 'text',
                        value: formState.permissions.canEditModules,
                        onChange: function (event) { updateField('permissions', 'canEditModules', event.target.value); },
                    }),
                    createElement('span', { className: 'revolt-control__help' }, __('Users with this capability will be able to manage Revolt modules.', 'revolt-slider-clone'))
                )
            ),
            createElement('div', { className: 'revolt-settings-actions' },
                createElement('button', { type: 'submit', className: 'button button-primary', disabled: saving }, saving ? __('Saving…', 'revolt-slider-clone') : __('Save settings', 'revolt-slider-clone')),
                createElement('button', {
                    type: 'button',
                    className: 'button button-secondary',
                    onClick: resetToDefaults,
                    disabled: saving,
                }, __('Reset to defaults', 'revolt-slider-clone'))
            )
        );
    };

    const AppShell = function (props) {
        return createElement('div', { className: 'revolt-admin-shell' },
            createElement(Header, { view: props.view }),
            createElement('main', { className: 'revolt-admin-shell__main' }, (function () {
                switch (props.view) {
                    case 'modules':
                        return createElement(ModulesView, { modules: props.data.modules, adminUrls: props.data.adminUrls, i18n: props.data.i18n });
                    case 'templates':
                        return createElement(TemplatesView, { templates: props.data.templates, adminUrls: props.data.adminUrls });
                    case 'addons':
                        return createElement(AddonsView, { addons: props.data.addons });
                    case 'settings':
                        return createElement(SettingsView, { settings: props.data.settings });
                    default:
                        return createElement(ModulesView, { modules: props.data.modules, adminUrls: props.data.adminUrls, i18n: props.data.i18n });
                }
            })())
        );
    };

    const mount = function (selector, view) {
        const target = document.querySelector(selector);
        if (!target) {
            return;
        }

        if (typeof createRoot === 'function') {
            if (!target.__revoltRoot) {
                target.__revoltRoot = createRoot(target);
            }
            target.__revoltRoot.render(createElement(AppShell, { view: view, data: data }));
        } else if (typeof renderLegacy === 'function') {
            renderLegacy(createElement(AppShell, { view: view, data: data }), target);
        }
    };

    document.addEventListener('DOMContentLoaded', function () {
        mount('#revolt-slider-app', 'modules');
        mount('#revolt-templates-app', 'templates');
        mount('#revolt-addons-app', 'addons');
        mount('#revolt-settings-app', 'settings');
    });
})();
