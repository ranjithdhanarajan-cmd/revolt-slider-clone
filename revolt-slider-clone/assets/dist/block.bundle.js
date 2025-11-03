(function () {
    const { registerBlockType } = wp.blocks;
    const { __ } = wp.i18n;
    const { useSelect } = wp.data;
    const { SelectControl, Placeholder } = wp.components;
    const { createElement: el, useState } = wp.element;

    registerBlockType('revolt/module', {
        title: __('Revolt Module', 'revolt-slider-clone'),
        icon: 'images-alt2',
        category: 'widgets',
        attributes: {
            moduleId: {
                type: 'number',
            },
        },
        edit: function (props) {
            const modules = useSelect(function (select) {
                const records = select('core').getEntityRecords('postType', 'revolt_module', { per_page: -1 });
                return records || [];
            }, []);

            const options = [{ label: __('Select…', 'revolt-slider-clone'), value: 0 }].concat(
                modules.map(function (module) {
                    return { label: module.title.rendered, value: module.id };
                })
            );

            return el(
                Placeholder,
                { label: __('Revolt Module', 'revolt-slider-clone'), className: 'revolt-block-placeholder' },
                el(SelectControl, {
                    label: __('Select module', 'revolt-slider-clone'),
                    value: props.attributes.moduleId,
                    options: options,
                    onChange: function (value) {
                        props.setAttributes({ moduleId: parseInt(value, 10) });
                    },
                })
            );
        },
        save: function () {
            return null;
        },
    });
})();
revolt-slider-clone/assets/dist/frontend.bundle.js
New
+57
-0

(function () {
    const modules = document.querySelectorAll('.revolt-module');

    const playLayerAnimation = function (layer, animation) {
        if (!animation) {
            return;
        }

        const to = animation.to || {};
        const duration = animation.duration || 600;
        const easing = animation.easing || 'ease-out';

        layer.style.transition = 'all ' + duration + 'ms ' + easing;
        if (to.opacity !== undefined) {
            layer.style.opacity = to.opacity;
        }
        if (to.y !== undefined) {
            layer.style.transform = 'translateY(' + to.y + 'px)';
        }
    };

    const prepareLayer = function (layer, animation) {
        if (!animation || !animation.from) {
            return;
        }

        const from = animation.from;
        if (from.opacity !== undefined) {
            layer.style.opacity = from.opacity;
        }
        if (from.y !== undefined) {
            layer.style.transform = 'translateY(' + from.y + 'px)';
        }
    };

    modules.forEach(function (module) {
        module.querySelectorAll('.revolt-layer').forEach(function (layer) {
            const data = layer.getAttribute('data-animations');
            if (!data) {
                return;
            }

            try {
                const animations = JSON.parse(data);
                if (animations.length) {
                    prepareLayer(layer, animations[0]);
                    setTimeout(function () {
                        playLayerAnimation(layer, animations[0]);
                    }, 100);
                }
            } catch (error) {
                // eslint-disable-next-line no-console
                console.warn('Revolt Slider animation parse error', error);
            }
        });
    });
})();
