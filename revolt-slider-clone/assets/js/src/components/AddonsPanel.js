import React from 'react';
import PropTypes from 'prop-types';

const AddonsPanel = ({ addons = {} }) => (
    <div className="revolt-addons">
        <h1>Available Add-ons</h1>
        <ul>
            {Object.entries(addons).map(([slug, addon]) => (
                <li key={slug}>
                    <strong>{addon.name}</strong>
                    <p>{addon.description}</p>
                </li>
            ))}
        </ul>
    </div>
);

AddonsPanel.propTypes = {
    addons: PropTypes.object,
};

export default AddonsPanel;
