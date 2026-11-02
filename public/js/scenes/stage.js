import * as THREE from 'three';

export function mount(canvas, setup) {
    const root = canvas.closest('[data-scene]');
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const light = window.matchMedia('(max-width: 760px)').matches
        || (navigator.hardwareConcurrency || 8) <= 4;

    let renderer;
    try {
        renderer = new THREE.WebGLRenderer({
            canvas,
            antialias: !light,
            alpha: true,
            powerPreference: 'high-performance'
        });
    } catch (error) {
        return null;
    }

    const gl = renderer.getContext();
    if (!gl) {
        renderer.dispose();
        return null;
    }

    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, light ? 1 : 1.5));
    renderer.setClearColor(0x000000, 0);
    renderer.outputColorSpace = THREE.SRGBColorSpace;
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.08;

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(30, 1, 0.1, 80);

    function resize() {
        const viewport = canvas.parentElement;
        const width = viewport.clientWidth;
        const height = viewport.clientHeight;
        if (!width || !height) {
            return;
        }
        camera.aspect = width / height;
        camera.updateProjectionMatrix();
        renderer.setSize(width, height, false);
        if (!running) {
            renderer.render(scene, camera);
        }
    }

    if (window.gsap) {
        window.gsap.ticker.lagSmoothing(0);
        window.gsap.ticker.remove(window.gsap.updateRoot);
    }

    const api = setup({ THREE, scene, camera, renderer, reduced, light });
    if (window.gsap) {
        window.gsap.ticker.remove(window.gsap.updateRoot);
    }
    let running = false;
    let cancel = function () {};
    let visible = true;
    let sawVisible = !document.hidden;

    function frame(now) {
        if (!running) {
            return;
        }
        if (window.gsap) {
            window.gsap.updateRoot(now / 1000);
        }
        api.tick?.(now);
        renderer.render(scene, camera);
        queue();
    }

    function queue() {
        let settled = false;
        const timer = setTimeout(function () {
            if (settled) {
                return;
            }
            settled = true;
            cancelAnimationFrame(raf);
            frame(performance.now());
        }, 40);
        const raf = requestAnimationFrame(function (now) {
            if (settled) {
                return;
            }
            settled = true;
            clearTimeout(timer);
            frame(now);
        });
        cancel = function () {
            settled = true;
            cancelAnimationFrame(raf);
            clearTimeout(timer);
        };
    }

    function start() {
        if (running || reduced) {
            return;
        }
        running = true;
        api.resume?.();
        queue();
    }

    function stop() {
        if (!running) {
            return;
        }
        running = false;
        cancel();
        api.pause?.();
    }

    const observer = new ResizeObserver(resize);
    observer.observe(canvas.parentElement);
    resize();

    if ('IntersectionObserver' in window) {
        const io = new IntersectionObserver(function (entries) {
            visible = entries[0].isIntersecting;
            if (!visible || (document.hidden && sawVisible)) {
                stop();
                return;
            }
            start();
        }, { threshold: 0.12 });
        io.observe(root);
        api._io = io;
    } else if (!document.hidden) {
        start();
    }

    document.addEventListener('visibilitychange', onHide);
    function onHide() {
        if (!document.hidden) {
            sawVisible = true;
            if (visible) {
                start();
            }
            return;
        }
        if (sawVisible) {
            stop();
        }
    }

    if (reduced) {
        api.still?.();
        resize();
        renderer.render(scene, camera);
    } else {
        start();
    }

    canvas.addEventListener('webglcontextlost', function (event) {
        event.preventDefault();
        stop();
        root.classList.remove('is-live');
    });

    window.addEventListener('pagehide', function () {
        stop();
        observer.disconnect();
        api._io?.disconnect();
        document.removeEventListener('visibilitychange', onHide);
        api.dispose?.();
        renderer.dispose();
    }, { once: true });

    return api;
}

export function lights(THREE, scene) {
    scene.add(new THREE.HemisphereLight(0xf8fbfe, 0xc3ced8, 0.95));
    const key = new THREE.DirectionalLight(0xffffff, 1.7);
    key.position.set(3.5, 6.5, 4.5);
    scene.add(key);
    const fill = new THREE.DirectionalLight(0xd7e4f0, 0.55);
    fill.position.set(-4.5, 2.2, -3);
    scene.add(fill);
}

export function steel(THREE, color) {
    return new THREE.MeshStandardMaterial({
        color: color || 0xb7c3cc,
        metalness: 0.42,
        roughness: 0.38
    });
}
