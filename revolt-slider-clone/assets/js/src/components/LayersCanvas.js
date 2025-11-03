import React from 'react';
import PropTypes from 'prop-types';

const LayersCanvas = ({ device, onSelectLayer }) => {
    const handleSelect = (event) => {
        const { layerId } = event.currentTarget.dataset;
        if (layerId) {
            onSelectLayer(layerId);
        }
    };

    return (
        <div className={`revolt-canvas revolt-canvas--${device}`}>
            <div className="revolt-canvas__surface">
                <button type="button" data-layer-id="layer-1" className="revolt-layer-thumb" onClick={handleSelect}>
                    Hero Heading
                </button>
                <button type="button" data-layer-id="layer-2" className="revolt-layer-thumb" onClick={handleSelect}>
                    Call to Action Button
                </button>
            </div>
        </div>
    );
};

LayersCanvas.propTypes = {
    device: PropTypes.string.isRequired,
    onSelectLayer: PropTypes.func.isRequired,
};

export default LayersCanvas;
