const revolts = document.querySelectorAll('.revolt-module');

const parseAnimations = (layer) => {
    const data = layer.dataset.animations;
    if (!data) {
        return [];
    }

    try {
        return JSON.parse(data);
    } catch (e) {
        return [];
    }
};

const applyInitialState = (layer, animation) => {
    if (!animation || !animation.from) {
        return;
    }

    const style = layer.style;
    if (animation.from.opacity !== undefined) {
        style.opacity = animation.from.opacity;
    }
    if (animation.from.y !== undefined) {
        style.transform = `translateY(${animation.from.y}px)`;
    }
};

const playAnimation = (layer, animation) => {
    if (!animation) {
        return;
    }

    const { to, duration = 600, easing = 'ease-out' } = animation;

    requestAnimationFrame(() => {
        layer.style.transition = `all ${duration}ms ${easing}`;
        if (to.opacity !== undefined) {
            layer.style.opacity = to.opacity;
        }
        if (to.y !== undefined) {
            layer.style.transform = `translateY(${to.y}px)`;
        }
    });
};

const initModule = (module) => {
    const layers = module.querySelectorAll('.revolt-layer');
    layers.forEach((layer) => {
        const animations = parseAnimations(layer);
        if (!animations.length) {
            return;
        }

        applyInitialState(layer, animations[0]);
        setTimeout(() => playAnimation(layer, animations[0]), 100);
    });
};

revolts.forEach((module) => initModule(module));
