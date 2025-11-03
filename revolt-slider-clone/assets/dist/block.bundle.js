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
