import { mount, lights, steel } from './stage.js';

const gsap = window.gsap;
const root = document.querySelector('[data-scene="strength"]');
const canvas = root && root.querySelector('canvas');
if (canvas && gsap) {
    const view = mount(canvas, build);
    if (view) {
        root.classList.add('is-live');
    }
}

function build({ THREE, scene, camera, reduced, light }) {
    lights(THREE, scene);
    camera.position.set(0.2, 2.7, 6.4);
    camera.lookAt(0, 0, 0);

    const detail = light ? [36, 5] : [52, 7];
    const built = plate(detail[0], detail[1]);
    const geometry = new THREE.BufferGeometry();
    geometry.setAttribute('position', new THREE.BufferAttribute(built.positions, 3));
    geometry.setIndex(built.indices);
    geometry.setAttribute('color', new THREE.BufferAttribute(new Float32Array(built.stress.length * 3), 3));
    geometry.computeVertexNormals();
    const rest = built.positions.slice();

    const material = new THREE.MeshStandardMaterial({
        vertexColors: true,
        metalness: 0.38,
        roughness: 0.42,
        transparent: true,
        opacity: reduced ? 1 : 0
    });
    const plateMesh = new THREE.Mesh(geometry, material);
    const wire = new THREE.Mesh(geometry, new THREE.MeshBasicMaterial({
        color: 0x0e2a47,
        wireframe: true,
        transparent: true,
        opacity: 0,
        polygonOffset: true,
        polygonOffsetFactor: -1,
        polygonOffsetUnits: -1
    }));

    const model = new THREE.Group();
    model.add(plateMesh, wire);
    scene.add(model);

    const gripMat = steel(THREE, 0x8d9baa);
    const grips = new THREE.Group();
    [-1, 1].forEach(function (side) {
        const grip = new THREE.Mesh(new THREE.BoxGeometry(0.16, 0.62, 0.28), gripMat);
        grip.position.set(side * 2.32, 0, 0);
        grips.add(grip);
    });
    grips.visible = reduced;
    scene.add(grips);

    const arrowMat = new THREE.MeshStandardMaterial({ color: 0x0e2a47, metalness: 0.2, roughness: 0.4, transparent: true, opacity: reduced ? 1 : 0 });
    gripMat.transparent = true;
    gripMat.opacity = reduced ? 1 : 0;
    const arrows = new THREE.Group();
    arrows.add(arrow(THREE, arrowMat, 1), arrow(THREE, arrowMat, -1));
    arrows.visible = reduced;
    scene.add(arrows);

    const shadow = new THREE.Mesh(
        new THREE.CircleGeometry(2.3, 32),
        new THREE.MeshBasicMaterial({ color: 0x0e2a47, transparent: true, opacity: 0.06, depthWrite: false })
    );
    shadow.rotation.x = -Math.PI / 2;
    shadow.position.y = -1.42;
    shadow.scale.z = 0.35;
    scene.add(shadow);

    const phase = root.querySelector('[data-phase]');
    const state = { reveal: reduced ? 1 : 0, mesh: reduced ? 1 : 0, deflect: reduced ? 1 : 0, stress: reduced ? 1 : 0 };
    let timeline = null;
    let spin = null;

    function paint() {
        material.opacity = state.reveal;
        wire.material.opacity = 0.42 * state.mesh;
        gripMat.opacity = state.mesh;
        arrowMat.opacity = Math.min(1, Math.max(0, (state.mesh - 0.72) / 0.28));
        grips.visible = state.mesh > 0.04;
        arrows.visible = arrowMat.opacity > 0.04;
        const color = geometry.attributes.color;
        const pos = geometry.attributes.position;
        const count = built.stress.length;
        for (let i = 0; i < count; i += 1) {
            const x = rest[i * 3];
            const y = rest[i * 3 + 1];
            const z = rest[i * 3 + 2];
            pos.setXYZ(
                i,
                x * (1 + 0.055 * state.deflect),
                y * (1 - 0.02 * state.deflect),
                z * (1 - 0.12 * state.deflect)
            );
            const mix = built.stress[i] * state.stress;
            const tone = sample(mix);
            color.setXYZ(i, tone[0], tone[1], tone[2]);
        }
        pos.needsUpdate = true;
        color.needsUpdate = true;
        if (phase) {
            phase.textContent = label(state);
        }
    }

    paint();

    if (!reduced) {
        timeline = gsap.timeline({ repeat: -1, defaults: { ease: 'power1.inOut' } });
        timeline.to(state, { reveal: 1, duration: 0.9, onUpdate: paint });
        timeline.to(state, { mesh: 1, duration: 1.5, onUpdate: paint });
        timeline.to({}, { duration: 0.45 });
        timeline.to(state, { deflect: 1, duration: 1.7, onUpdate: paint });
        timeline.to(state, { stress: 1, duration: 1.6, onUpdate: paint });
        timeline.to({}, { duration: 1.5 });
        timeline.to(state, { stress: 0, deflect: 0, duration: 1.5, onUpdate: paint });
        timeline.to(state, { mesh: 0, reveal: 0, duration: 0.8, onUpdate: paint });
        spin = gsap.to(model.rotation, { y: 0.55, duration: 9, yoyo: true, repeat: -1, ease: 'sine.inOut' });
        model.rotation.y = -0.35;
        timeline.pause();
        spin.pause();
    } else {
        model.rotation.y = -0.2;
        paint();
    }

    return {
        pause: function () {
            timeline?.pause();
            spin?.pause();
        },
        resume: function () {
            timeline?.resume();
            spin?.resume();
        },
        dispose: function () {
            timeline?.kill();
            spin?.kill();
            geometry.dispose();
            material.dispose();
            wire.material.dispose();
            gripMat.dispose();
            arrowMat.dispose();
            grips.traverse(function (child) { child.geometry?.dispose(); });
            arrows.traverse(function (child) { child.geometry?.dispose(); });
            shadow.geometry.dispose();
            shadow.material.dispose();
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

    function id(layer, ring, i) {
        return layer * rings * na + ring * na + (i % na);
    }

    for (let layer = 0; layer < 2; layer += 1) {
        const z = layer === 0 ? 0.075 : -0.075;
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

    function quad(a, b, c, d) {
        indices.push(a, b, d, b, c, d);
    }

    for (let layer = 0; layer < 2; layer += 1) {
        for (let ring = 0; ring < nr; ring += 1) {
            for (let i = 0; i < na; i += 1) {
                const next = i + 1;
                const a = id(layer, ring, i);
                const b = id(layer, ring, next);
                const d = id(layer, ring + 1, i);
                const c = id(layer, ring + 1, next);
                if (layer === 0) {
                    quad(a, d, c, b);
                } else {
                    quad(a, b, c, d);
                }
            }
        }
    }

    for (let i = 0; i < na; i += 1) {
        const next = i + 1;
        quad(id(0, 0, i), id(0, 0, next), id(1, 0, next), id(1, 0, i));
        quad(id(0, nr, i), id(1, nr, i), id(1, nr, next), id(0, nr, next));
    }

    return {
        positions: new Float32Array(positions),
        indices: indices,
        stress: stress
    };
}

function outer(angle, hw, hh) {
    const c = Math.abs(Math.cos(angle));
    const s = Math.abs(Math.sin(angle));
    const tx = c < 1e-5 ? Infinity : hw / c;
    const ty = s < 1e-5 ? Infinity : hh / s;
    return Math.min(tx, ty);
}

function arrow(THREE, material, side) {
    const group = new THREE.Group();
    const shaft = new THREE.Mesh(new THREE.CylinderGeometry(0.018, 0.018, 0.62, 8), material);
    shaft.rotation.z = Math.PI / 2;
    const head = new THREE.Mesh(new THREE.ConeGeometry(0.055, 0.16, 10), material);
    head.rotation.z = side > 0 ? -Math.PI / 2 : Math.PI / 2;
    head.position.x = side * 0.39;
    shaft.position.x = side * 0.02;
    group.add(shaft, head);
    group.position.set(side * 2.55, 0.34, 0);
    return group;
}

function sample(amount) {
    const stops = [
        [0, [0.78, 0.83, 0.87]],
        [0.35, [0.49, 0.69, 0.8]],
        [0.62, [0.25, 0.48, 0.66]],
        [0.82, [0.12, 0.28, 0.45]],
        [1, [0.06, 0.14, 0.24]]
    ];
    const t = Math.min(1, Math.max(0, amount));
    for (let i = 1; i < stops.length; i += 1) {
        if (t <= stops[i][0]) {
            const span = stops[i][0] - stops[i - 1][0];
            const local = span === 0 ? 0 : (t - stops[i - 1][0]) / span;
            const a = stops[i - 1][1];
            const b = stops[i][1];
            return [
                a[0] + (b[0] - a[0]) * local,
                a[1] + (b[1] - a[1]) * local,
                a[2] + (b[2] - a[2]) * local
            ];
        }
    }
    return stops[stops.length - 1][1];
}

function label(state) {
    if (state.reveal < 0.95) {
        return 'Geometry';
    }
    if (state.mesh < 0.95) {
        return 'Mesh';
    }
    if (state.deflect < 0.08) {
        return 'Constraint · tension';
    }
    if (state.stress < 0.95) {
        return 'Deformation · stress';
    }
    return 'σ max 126 MPa · demo';
}
