import React from 'react';
import PropTypes from 'prop-types';

const TemplatesLibrary = ({ templates = [] }) => (
    <div className="revolt-templates">
        <h1>Templates Library</h1>
        <p>Select from professionally designed starting points.</p>
        <div className="revolt-templates__grid">
            {templates.map((template) => (
                <article key={template.slug} className="revolt-template-card">
                    <header>
                        <h2>{template.data?.title || template.slug}</h2>
                    </header>
                    <pre>{JSON.stringify(template.data, null, 2)}</pre>
                </article>
            ))}
        </div>
    </div>
);

TemplatesLibrary.propTypes = {
    templates: PropTypes.array,
};

export default TemplatesLibrary;
