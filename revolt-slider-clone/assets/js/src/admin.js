/* global RevoltSliderData */
import { render } from '@wordpress/element';
import App from './src/components/App';

const mount = (selector, component) => {
    const root = document.querySelector(selector);
    if (root) {
        render(component, root);
    }
};

document.addEventListener('DOMContentLoaded', () => {
    mount('#revolt-slider-app', <App data={RevoltSliderData} view="modules" />);
    mount('#revolt-templates-app', <App data={RevoltSliderData} view="templates" />);
    mount('#revolt-addons-app', <App data={RevoltSliderData} view="addons" />);
    mount('#revolt-settings-app', <App data={RevoltSliderData} view="settings" />);
});
