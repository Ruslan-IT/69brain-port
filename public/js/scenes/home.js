import { mount, lights } from './stage.js';

const gsap = window.gsap;
const root = document.querySelector('[data-scene="home"]');
const canvas = root && root.querySelector('canvas');
if (canvas && gsap) {
    const view = mount(canvas, build);
    if (view) {
        root.classList.add('is-live');
    }
}

function build({ THREE, scene, camera, reduced, light }) {
    lights(THREE, scene);
    camera.position.set(0.35, 2.15, 7.4);
    camera.lookAt(0, 0, 0);

    const built = plate(light ? 40 : 56, light ? 5 : 7);
    const geometry = new THREE.BufferGeometry();
    geometry.setAttribute('position', new THREE.BufferAttribute(built.positions, 3));
    geometry.setIndex(built.indices);
    geometry.setAttribute('color', new THREE.BufferAttribute(new Float32Array(built.stress.length * 3), 3));
    geometry.computeVertexNormals();
    const rest = built.positions.slice();

    const material = new THREE.MeshStandardMaterial({
        vertexColors: true,
        metalness: 0.38,
        roughness: 0.42
    });
    const mesh = new THREE.Mesh(geometry, material);
    const wire = new THREE.Mesh(geometry, new THREE.MeshBasicMaterial({
        color: 0x0e2a47,
        wireframe: true,
        transparent: true,
        opacity: reduced ? 0.4 : 0,
        polygonOffset: true,
        polygonOffsetFactor: -1,
        polygonOffsetUnits: -1
    }));
    const model = new THREE.Group();
    model.add(mesh, wire);
    model.rotation.y = -0.45;
    scene.add(model);

    const phase = root.querySelector('[data-phase]');
    const state = { mesh: reduced ? 1 : 0, deflect: reduced ? 1 : 0, stress: reduced ? 1 : 0 };
    paint();

    let timeline = null;
    let spin = null;
    if (!reduced) {
        timeline = gsap.timeline({ repeat: -1, defaults: { ease: 'power1.inOut' } });
        timeline.to(state, { mesh: 1, duration: 1.6, onUpdate: paint });
        timeline.to(state, { deflect: 1, duration: 1.8, onUpdate: paint });
        timeline.to(state, { stress: 1, duration: 1.8, onUpdate: paint });
        timeline.to({}, { duration: 2.4 });
        timeline.to(state, { stress: 0, deflect: 0, mesh: 0.15, duration: 1.6, onUpdate: paint });
        spin = gsap.fromTo(model.rotation, { y: -0.55 }, { y: 0.45, duration: 11, yoyo: true, repeat: -1, ease: 'sine.inOut' });
        timeline.pause();
        spin.pause();
    }

    function paint() {
        wire.material.opacity = 0.38 * state.mesh;
        const color = geometry.attributes.color;
        const pos = geometry.attributes.position;
        for (let i = 0; i < built.stress.length; i += 1) {
            const x = rest[i * 3];
            const y = rest[i * 3 + 1];
            const z = rest[i * 3 + 2];
            pos.setXYZ(i, x * (1 + 0.05 * state.deflect), y * (1 - 0.018 * state.deflect), z);
            const tone = sample(built.stress[i] * state.stress);
            color.setXYZ(i, tone[0], tone[1], tone[2]);
        }
        pos.needsUpdate = true;
        color.needsUpdate = true;
        if (phase) {
            phase.textContent = state.stress > 0.9 ? 'σ · условная карта' : (state.mesh > 0.85 ? 'Mesh · tension' : 'Geometry');
        }
    }

    return {
        pause: function () { timeline?.pause(); spin?.pause(); },
        resume: function () { timeline?.resume(); spin?.resume(); },
        dispose: function () {
            timeline?.kill();
            spin?.kill();
            geometry.dispose();
            material.dispose();
            wire.material.dispose();
        }
    };
}

function plate(na, nr) {
    const hw = 2.15;
    const hh = 1.22;
    const hole = 0.46;
    const positions = [];
    const indices = [];
    const stress = [];
    const rings = nr + 1;
    const id = function (layer, ring, i) {
        return layer * rings * na + ring * na + (i % na);
    };
    for (let layer = 0; layer < 2; layer += 1) {
        const z = layer === 0 ? 0.07 : -0.07;
        for (let ring = 0; ring <= nr; ring += 1) {
            const t = ring / nr;
            for (let i = 0; i < na; i += 1) {
                const angle = (i / na) * Math.PI * 2;
                const radius = hole + (outer(angle, hw, hh) - hole) * t;
                positions.push(Math.cos(angle) * radius, Math.sin(angle) * radius, z);
                stress.push(Math.pow(1 - t, 1.65));
            }
        }
    }
    const quad = function (a, b, c, d) { indices.push(a, b, d, b, c, d); };
    for (let layer = 0; layer < 2; layer += 1) {
        for (let ring = 0; ring < nr; ring += 1) {
            for (let i = 0; i < na; i += 1) {
                const a = id(layer, ring, i);
                const b = id(layer, ring, i + 1);
                const d = id(layer, ring + 1, i);
                const c = id(layer, ring + 1, i + 1);
                if (layer === 0) quad(a, d, c, b);
                else quad(a, b, c, d);
            }
        }
    }
    for (let i = 0; i < na; i += 1) {
        quad(id(0, 0, i), id(0, 0, i + 1), id(1, 0, i + 1), id(1, 0, i));
        quad(id(0, nr, i), id(1, nr, i), id(1, nr, i + 1), id(0, nr, i + 1));
    }
    return { positions: new Float32Array(positions), indices: indices, stress: stress };
}

function outer(angle, hw, hh) {
    const c = Math.abs(Math.cos(angle));
    const s = Math.abs(Math.sin(angle));
    return Math.min(c < 1e-5 ? Infinity : hw / c, s < 1e-5 ? Infinity : hh / s);
}

function sample(amount) {
    const stops = [[0, [0.78, 0.83, 0.87]], [0.4, [0.45, 0.66, 0.8]], [0.7, [0.18, 0.4, 0.6]], [1, [0.06, 0.14, 0.24]]];
    const t = Math.min(1, Math.max(0, amount));
    for (let i = 1; i < stops.length; i += 1) {
        if (t <= stops[i][0]) {
            const span = stops[i][0] - stops[i - 1][0];
            const local = span === 0 ? 0 : (t - stops[i - 1][0]) / span;
            return stops[i - 1][1].map(function (channel, k) {
                return channel + (stops[i][1][k] - channel) * local;
            });
        }
    }
    return stops[stops.length - 1][1];
}
