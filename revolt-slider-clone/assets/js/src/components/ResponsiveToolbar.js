import React from 'react';
import PropTypes from 'prop-types';

const devices = [
    { id: 'desktop', label: 'Desktop' },
    { id: 'tablet', label: 'Tablet' },
    { id: 'mobile', label: 'Mobile' },
];

const ResponsiveToolbar = ({ activeDevice, onChange }) => (
    <div className="revolt-responsive-toolbar">
        {devices.map((device) => (
            <button
                key={device.id}
                type="button"
                className={device.id === activeDevice ? 'is-active' : ''}
                onClick={() => onChange(device.id)}
            >
                {device.label}
            </button>
        ))}
    </div>
);

ResponsiveToolbar.propTypes = {
    activeDevice: PropTypes.string.isRequired,
    onChange: PropTypes.func.isRequired,
};

export default ResponsiveToolbar;
