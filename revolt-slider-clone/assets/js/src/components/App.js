import React from 'react';
import ModuleList from './ModuleList';
import ModuleEditor from './ModuleEditor';
import TemplatesLibrary from './TemplatesLibrary';
import AddonsPanel from './AddonsPanel';
import SettingsPanel from './SettingsPanel';

const App = ({ data, view }) => {
    if (view === 'modules') {
        return <ModuleEditor data={data} />;
    }

    if (view === 'templates') {
        return <TemplatesLibrary templates={data.templates} />;
    }

    if (view === 'addons') {
        return <AddonsPanel addons={data.addons} />;
    }

    if (view === 'settings') {
        return <SettingsPanel />;
    }

    return <ModuleList modules={data.modules} />;
};

export default App;
