import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import { SelectControl, Placeholder } from '@wordpress/components';

registerBlockType('revolt/module', {
    title: __('Revolt Module', 'revolt-slider-clone'),
    icon: 'images-alt2',
    category: 'widgets',
    attributes: {
        moduleId: {
            type: 'number',
        },
    },
    edit: ({ attributes, setAttributes }) => {
        const modules = useSelect((select) => {
            return select('core').getEntityRecords('postType', 'revolt_module', { per_page: -1 }) || [];
        }, []);

        return (
            <Placeholder label={__('Revolt Module', 'revolt-slider-clone')} className="revolt-block-placeholder">
                <SelectControl
                    label={__('Select module', 'revolt-slider-clone')}
                    value={attributes.moduleId}
                    options={[{ label: __('Select…', 'revolt-slider-clone'), value: 0 }].concat(
                        modules.map((module) => ({ label: module.title.rendered, value: module.id }))
                    )}
                    onChange={(value) => setAttributes({ moduleId: parseInt(value, 10) })}
                />
            </Placeholder>
        );
    },
    save: () => null,
});
