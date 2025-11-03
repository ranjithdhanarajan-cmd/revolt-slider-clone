import React from 'react';

const SettingsPanel = () => (
    <div className="revolt-settings">
        <h1>Global Settings</h1>
        <p>Configure caching, asset loading strategy, and global defaults.</p>
        <ul>
            <li>Lazy load images and videos</li>
            <li>Disable animations on mobile</li>
            <li>Global typography presets</li>
        </ul>
    </div>
);

export default SettingsPanel;
