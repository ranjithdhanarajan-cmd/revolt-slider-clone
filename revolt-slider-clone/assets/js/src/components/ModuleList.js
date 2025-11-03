import React from 'react';
import PropTypes from 'prop-types';

const ModuleList = ({ modules = [] }) => {
    return (
        <div className="revolt-module-list">
            <header className="revolt-module-list__header">
                <h1>Revolt Modules</h1>
                <p>Create, manage, and edit slider modules.</p>
            </header>
            {modules.length === 0 ? (
                <div className="revolt-empty">
                    <p>No modules yet. Use the "Create Module" button in the editor.</p>
                </div>
            ) : (
                <ul>
                    {modules.map((module) => (
                        <li key={module.id}>
                            <strong>{module.title}</strong> <em>{module.type}</em>
                        </li>
                    ))}
                </ul>
            )}
        </div>
    );
};

ModuleList.propTypes = {
    modules: PropTypes.array,
};

export default ModuleList;
