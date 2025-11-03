import React from 'react';
import PropTypes from 'prop-types';

const SidebarPanels = ({ selectedLayer }) => {
    return (
        <aside className="revolt-sidebar">
            <section>
                <h2>Module Settings</h2>
                <p>Configure module-level properties such as autoplay, navigation, and responsive behavior.</p>
            </section>
            <section>
                <h2>Slide Settings</h2>
                <p>Change slide background, duration, and transition.</p>
            </section>
            <section>
                <h2>Layer Settings</h2>
                {selectedLayer ? (
                    <p>Editing layer: {selectedLayer}</p>
                ) : (
                    <p>Select a layer to edit typography, colors, animations, and responsive overrides.</p>
                )}
            </section>
        </aside>
    );
};

SidebarPanels.propTypes = {
    selectedLayer: PropTypes.string,
};

export default SidebarPanels;
