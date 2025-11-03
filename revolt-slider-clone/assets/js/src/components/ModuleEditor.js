import React, { useState } from 'react';
import PropTypes from 'prop-types';
import TimelinePanel from './TimelinePanel';
import SidebarPanels from './SidebarPanels';
import ResponsiveToolbar from './ResponsiveToolbar';
import LayersCanvas from './LayersCanvas';

const ModuleEditor = ({ data }) => {
    const [device, setDevice] = useState('desktop');
    const [selectedLayer, setSelectedLayer] = useState(null);

    const config = data.schema ? data.schema.module : {};

    return (
        <div className="revolt-editor">
            <ResponsiveToolbar activeDevice={device} onChange={setDevice} />
            <div className="revolt-editor__workspace">
                <LayersCanvas device={device} onSelectLayer={setSelectedLayer} />
                <SidebarPanels selectedLayer={selectedLayer} />
            </div>
            <TimelinePanel />
        </div>
    );
};

ModuleEditor.propTypes = {
    data: PropTypes.object.isRequired,
};

export default ModuleEditor;
