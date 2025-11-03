(function () {
    const modules = document.querySelectorAll('.revolt-module');

    const playLayerAnimation = function (layer, animation) {
        if (!animation) {
            return;
        }

        const to = animation.to || {};
        const duration = animation.duration || 600;
        const easing = animation.easing || 'ease-out';

        layer.style.transition = 'all ' + duration + 'ms ' + easing;
        if (to.opacity !== undefined) {
            layer.style.opacity = to.opacity;
        }
        if (to.y !== undefined) {
            layer.style.transform = 'translateY(' + to.y + 'px)';
        }
    };

    const prepareLayer = function (layer, animation) {
        if (!animation || !animation.from) {
            return;
        }

        const from = animation.from;
        if (from.opacity !== undefined) {
            layer.style.opacity = from.opacity;
        }
        if (from.y !== undefined) {
            layer.style.transform = 'translateY(' + from.y + 'px)';
        }
    };

    modules.forEach(function (module) {
        module.querySelectorAll('.revolt-layer').forEach(function (layer) {
            const data = layer.getAttribute('data-animations');
            if (!data) {
                return;
            }

            try {
                const animations = JSON.parse(data);
                if (animations.length) {
                    prepareLayer(layer, animations[0]);
                    setTimeout(function () {
                        playLayerAnimation(layer, animations[0]);
                    }, 100);
                }
            } catch (error) {
                // eslint-disable-next-line no-console
                console.warn('Revolt Slider animation parse error', error);
            }
        });
    });
})();
