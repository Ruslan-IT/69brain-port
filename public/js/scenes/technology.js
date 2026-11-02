import { mount, lights, steel } from './stage.js';

const gsap = window.gsap;
const root = document.querySelector('[data-scene="technology"]');
const canvas = root && root.querySelector('canvas');
if (canvas && gsap) {
    const view = mount(canvas, build);
    if (view) {
        root.classList.add('is-live');
    }
}

function build({ THREE, scene, camera, reduced, light }) {
    lights(THREE, scene);
    camera.position.set(2.6, 2.5, 4.4);
    camera.lookAt(0, 0.35, 0);

    const stamp = stampRig(THREE, light, reduced);
    const laser = laserRig(THREE, light, reduced);
    scene.add(stamp.group, laser.group);

    const phase = root.querySelector('[data-phase]');
    const buttons = root.querySelectorAll('[data-mode]');
    let mode = 'stamp';
    let timeline = null;
    let spin = null;
    let started = false;

    function play(next) {
        mode = next;
        timeline?.kill();
        stamp.group.visible = mode === 'stamp';
        laser.group.visible = mode === 'laser';
        buttons.forEach(function (button) {
            const on = button.getAttribute('data-mode') === mode;
            button.classList.toggle('is-on', on);
            button.setAttribute('aria-selected', on ? 'true' : 'false');
        });
        if (reduced) {
            if (mode === 'stamp') {
                stamp.at(1);
            } else {
                laser.at(1);
            }
            if (phase) {
                phase.textContent = mode === 'stamp' ? 'Formed · demo' : 'Nested · demo';
            }
            return;
        }
        timeline = mode === 'stamp'
            ? stamp.timeline(gsap, phase)
            : laser.timeline(gsap, phase);
        if (!started) {
            timeline.pause();
        }
    }

    buttons.forEach(function (button) {
        button.addEventListener('click', function () {
            if (button.getAttribute('data-mode') !== mode) {
                play(button.getAttribute('data-mode'));
            }
        });
    });

    play('stamp');
    if (!reduced) {
        spin = gsap.to(scene.rotation, { y: 0.18, duration: 8, yoyo: true, repeat: -1, ease: 'sine.inOut' });
        spin.pause();
    }

    return {
        pause: function () {
            timeline?.pause();
            spin?.pause();
        },
        resume: function () {
            started = true;
            timeline?.resume();
            spin?.resume();
        },
        dispose: function () {
            timeline?.kill();
            spin?.kill();
            stamp.dispose();
            laser.dispose();
        }
    };
}

function stampRig(THREE, light, reduced) {
    const group = new THREE.Group();
    const die = new THREE.Mesh(new THREE.BoxGeometry(2.5, 0.36, 1.55), steel(THREE, 0x7e8c9a));
    die.position.y = -0.28;
    const punch = new THREE.Mesh(new THREE.CylinderGeometry(0.26, 0.34, 0.7, light ? 12 : 20), steel(THREE, 0x5d6c7b));
    punch.position.y = reduced ? 0.55 : 1.2;
    const cols = light ? 22 : 34;
    const rows = light ? 12 : 18;
    const sheetGeo = new THREE.PlaneGeometry(2.15, 1.25, cols, rows);
    sheetGeo.rotateX(-Math.PI / 2);
    const rest = sheetGeo.attributes.position.array.slice();
    const sheet = new THREE.Mesh(sheetGeo, new THREE.MeshStandardMaterial({
        color: 0xd5dee6,
        metalness: 0.55,
        roughness: 0.32,
        side: THREE.DoubleSide
    }));
    sheet.position.y = 0.18;
    group.add(die, punch, sheet);

    function at(depth) {
        punch.position.y = 1.2 - 0.65 * depth;
        const pos = sheetGeo.attributes.position;
        for (let i = 0; i < pos.count; i += 1) {
            const x = rest[i * 3];
            const z = rest[i * 3 + 2];
            const bowl = Math.max(0, 1 - Math.sqrt((x / 0.42) ** 2 + (z / 0.32) ** 2));
            const shaped = bowl * bowl * (3 - 2 * bowl);
            pos.setY(i, rest[i * 3 + 1] - 0.28 * depth * shaped);
        }
        pos.needsUpdate = true;
        sheetGeo.computeVertexNormals();
    }

    if (reduced) {
        at(1);
    }

    return {
        group: group,
        at: at,
        timeline: function (gsap, phase) {
            const state = { depth: 0 };
            at(0);
            return gsap.timeline({ repeat: -1 })
                .to(state, {
                    depth: 1,
                    duration: 2.4,
                    ease: 'power2.inOut',
                    onUpdate: function () {
                        at(state.depth);
                        if (phase) {
                            phase.textContent = 'Stroke ' + Math.round(state.depth * 100) + '%';
                        }
                    }
                })
                .to({}, { duration: 1.1 })
                .to(state, {
                    depth: 0,
                    duration: 1.6,
                    ease: 'power1.inOut',
                    onUpdate: function () {
                        at(state.depth);
                    }
                })
                .to({}, { duration: 0.6 });
        },
        dispose: function () {
            die.geometry.dispose();
            die.material.dispose();
            punch.geometry.dispose();
            punch.material.dispose();
            sheetGeo.dispose();
            sheet.material.dispose();
        }
    };
}

function laserRig(THREE, light) {
    const group = new THREE.Group();
    group.visible = false;
    const sheet = new THREE.Mesh(
        new THREE.BoxGeometry(2.7, 0.04, 1.7),
        steel(THREE, 0xc5d0d8)
    );
    const contours = paths();
    const cutMaterial = new THREE.LineBasicMaterial({ color: 0x9a3f24 });
    const lines = contours.map(function (flat) {
        const geo = new THREE.BufferGeometry();
        geo.setAttribute('position', new THREE.BufferAttribute(new Float32Array(flat), 3));
        geo.setDrawRange(0, 0);
        const line = new THREE.Line(geo, cutMaterial);
        line.position.y = 0.035;
        group.add(line);
        return { geo: geo, flat: flat, count: flat.length / 3 };
    });
    const total = lines.reduce(function (sum, line) { return sum + line.count; }, 0);

    const head = new THREE.Group();
    const body = new THREE.Mesh(new THREE.BoxGeometry(0.16, 0.22, 0.16), steel(THREE, 0x3e4c5c));
    const tip = new THREE.Mesh(
        new THREE.CylinderGeometry(0.02, 0.035, 0.12, 8),
        new THREE.MeshStandardMaterial({ color: 0x9a3f24, emissive: 0x9a3f24, emissiveIntensity: 0.35, roughness: 0.4 })
    );
    tip.position.y = -0.16;
    head.add(body, tip);
    head.position.y = 0.42;
    group.add(sheet, head);

    function at(progress) {
        let remaining = Math.max(0, Math.min(0.9999, progress)) * total;
        let placed = false;
        lines.forEach(function (line) {
            if (remaining >= line.count) {
                line.geo.setDrawRange(0, line.count);
                remaining -= line.count;
                return;
            }
            if (remaining > 0) {
                const index = Math.min(line.count - 1, Math.floor(remaining));
                line.geo.setDrawRange(0, index + 1);
                head.position.x = line.flat[index * 3];
                head.position.z = line.flat[index * 3 + 2];
                placed = true;
                remaining = 0;
                return;
            }
            line.geo.setDrawRange(0, 0);
        });
        if (!placed) {
            const last = lines[lines.length - 1];
            const index = last.count - 1;
            head.position.x = last.flat[index * 3];
            head.position.z = last.flat[index * 3 + 2];
        }
    }

    return {
        group: group,
        at: at,
        timeline: function (gsap, phase) {
            const state = { progress: 0 };
            at(0);
            return gsap.timeline({ repeat: -1, defaults: { ease: 'none' } })
                .to(state, {
                    progress: 1,
                    duration: 6.5,
                    onUpdate: function () {
                        at(state.progress);
                        if (phase) {
                            phase.textContent = 'Toolpath ' + Math.round(state.progress * 100) + '%';
                        }
                    }
                })
                .to({}, { duration: 1.2 })
                .set(state, { progress: 0 });
        },
        dispose: function () {
            sheet.geometry.dispose();
            sheet.material.dispose();
            cutMaterial.dispose();
            lines.forEach(function (line) { line.geo.dispose(); });
            body.geometry.dispose();
            body.material.dispose();
            tip.geometry.dispose();
            tip.material.dispose();
        }
    };
}

function paths() {
    return [
        rect(-1.05, -0.15, 0.7, 0.7),
        rect(-0.15, 0.15, 0.85, 0.5),
        circle(0.85, -0.22, 0.28, 18),
        rect(0.45, -0.62, 0.42, 0.28)
    ];
}

function rect(x, z, w, h) {
    const points = [
        [x, z],
        [x + w, z],
        [x + w, z + h],
        [x, z + h],
        [x, z]
    ];
    return points.flatMap(function (point) {
        return [point[0], 0, point[1]];
    });
}

function circle(x, z, radius, steps) {
    const points = [];
    for (let i = 0; i <= steps; i += 1) {
        const angle = (i / steps) * Math.PI * 2;
        points.push(x + Math.cos(angle) * radius, 0, z + Math.sin(angle) * radius);
    }
    return points;
}
