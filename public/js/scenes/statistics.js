import { mount, lights } from './stage.js';

const gsap = window.gsap;
const root = document.querySelector('[data-scene="statistics"]');
const canvas = root && root.querySelector('canvas');
if (canvas && gsap) {
    const view = mount(canvas, build);
    if (view) {
        root.classList.add('is-live');
    }
}

function build({ THREE, scene, camera, reduced, light }) {
    lights(THREE, scene);
    camera.position.set(4.8, 2.7, 4.4);
    camera.lookAt(-0.1, 0.05, 0);

    const count = light ? 96 : 180;
    const cloud = samples(count, 0.22, 4);
    const next = samples(count, 0.55, 19);
    const from = cloud.map(function (row) {
        return { x: row.x * 0.2, y: row.y + 1.8, z: row.z * 2.4 };
    });
    const positions = new Float32Array(count * 3);
    const colors = new Float32Array(count * 3);
    const geometry = new THREE.BufferGeometry();
    geometry.setAttribute('position', new THREE.BufferAttribute(positions, 3));
    geometry.setAttribute('color', new THREE.BufferAttribute(colors, 3));

    const points = new THREE.Points(geometry, new THREE.PointsMaterial({
        size: light ? 0.09 : 0.11,
        map: dot(THREE),
        transparent: true,
        alphaTest: 0.15,
        vertexColors: true,
        depthWrite: false
    }));

    const line = makeLine(THREE, 28);
    const upper = makeLine(THREE, 28);
    const lower = makeLine(THREE, 28);
    upper.material.color.set(0x1f4e79);
    lower.material.color.set(0x1f4e79);
    const plot = new THREE.Group();
    plot.add(points, line, upper, lower);
    scene.add(plot);

    const grid = new THREE.GridHelper(4.6, 10, 0xc5d2de, 0xd5dee6);
    grid.position.y = -1.45;
    scene.add(grid);

    const phase = root.querySelector('[data-phase]');
    const fit = regression(cloud);
    const shown = { arrive: reduced ? 1 : 0, line: reduced ? 1 : 0, band: reduced ? 1 : 0, blend: 0 };
    let timeline = null;
    let spin = null;

    function rowsNow() {
        return shown.blend > 0 ? blendPoints(cloud, next, shown.blend) : cloud;
    }

    function apply() {
        const rows = rowsNow();
        const current = shown.blend > 0 ? regression(rows) : fit;
        const band = 0.48;
        let outliers = 0;
        for (let i = 0; i < count; i += 1) {
            const delay = i / count;
            const local = Math.min(1, Math.max(0, (shown.arrive - delay * 0.55) / 0.45));
            const eased = local * local * (3 - 2 * local);
            const start = from[i];
            const row = rows[i];
            const x = start.x + (row.x - start.x) * eased;
            const y = start.y + (row.y - start.y) * eased;
            const z = start.z + (row.z - start.z) * eased;
            positions[i * 3] = x;
            positions[i * 3 + 1] = y;
            positions[i * 3 + 2] = z;
            const residual = Math.abs(row.y - (current.intercept + current.slope * row.x));
            const hot = shown.band * (residual > band ? 1 : 0);
            if (hot && eased > 0.9) {
                outliers += 1;
            }
            const depth = (z + 1.05) / 2.1;
            colors[i * 3] = 0.05 + hot * 0.62 + depth * 0.08;
            colors[i * 3 + 1] = 0.16 + (1 - hot) * 0.22;
            colors[i * 3 + 2] = 0.28 + (1 - hot) * (0.18 + depth * 0.22);
        }
        geometry.attributes.position.needsUpdate = true;
        geometry.attributes.color.needsUpdate = true;
        trace(line, current, 0, shown.line);
        trace(upper, current, band, shown.band);
        trace(lower, current, -band, shown.band);
        if (phase) {
            const drawn = Math.round(shown.arrive * count);
            phase.textContent = 'n = ' + drawn + '   r = ' + (shown.line * current.r).toFixed(2) + '   выбросы ' + outliers + ' · условно';
        }
    }

    apply();
    if (!reduced) {
        timeline = gsap.timeline({ repeat: -1, defaults: { ease: 'power1.inOut' } });
        timeline.to(shown, { arrive: 1, duration: 2.8, onUpdate: apply });
        timeline.to(shown, { line: 1, duration: 1.3, onUpdate: apply });
        timeline.to(shown, { band: 1, duration: 1.1, onUpdate: apply });
        timeline.to({}, { duration: 1.5 });
        timeline.to(shown, { blend: 1, duration: 2.2, onUpdate: apply });
        timeline.to({}, { duration: 1.4 });
        timeline.to(shown, { arrive: 0, line: 0, band: 0, blend: 0, duration: 1, onUpdate: apply });
        spin = gsap.to(plot.rotation, { y: 0.7, duration: 12, yoyo: true, repeat: -1, ease: 'sine.inOut' });
        timeline.pause();
        spin.pause();
    }

    return {
        pause: function () { timeline?.pause(); spin?.pause(); },
        resume: function () { timeline?.resume(); spin?.resume(); },
        dispose: function () {
            timeline?.kill();
            spin?.kill();
            geometry.dispose();
            points.material.map.dispose();
            points.material.dispose();
            [line, upper, lower].forEach(function (item) {
                item.geometry.dispose();
                item.material.dispose();
            });
            grid.geometry.dispose();
        }
    };
}

function makeLine(THREE, segments) {
    const geometry = new THREE.BufferGeometry();
    geometry.setAttribute('position', new THREE.BufferAttribute(new Float32Array(segments * 3), 3));
    geometry.setDrawRange(0, 0);
    return new THREE.Line(geometry, new THREE.LineBasicMaterial({ color: 0x0e2a47 }));
}

function trace(line, fit, offset, grow) {
    const pos = line.geometry.attributes.position;
    const n = pos.count;
    for (let i = 0; i < n; i += 1) {
        const t = i / (n - 1);
        const x = -1.45 + 2.9 * t;
        pos.setXYZ(i, x, fit.intercept + fit.slope * x + offset, 0);
    }
    pos.needsUpdate = true;
    const visible = grow <= 0.02 ? 0 : Math.max(2, Math.round(grow * n));
    line.geometry.setDrawRange(0, visible);
}

function samples(count, noise, seedStart) {
    const rows = [];
    let seed = seedStart;
    function rand() {
        seed = (seed * 16807) % 2147483647;
        return (seed - 1) / 2147483646;
    }
    for (let i = 0; i < count; i += 1) {
        const x = -1.4 + (2.8 * i) / (count - 1);
        const spike = rand() > 0.94 ? (rand() > 0.5 ? 0.85 : -0.85) : 0;
        const y = 0.72 * x + (rand() - 0.5) * noise * 2 + spike;
        rows.push({
            x: x,
            y: Math.max(-1.25, Math.min(1.25, y)),
            z: (rand() - 0.5) * 2.05
        });
    }
    return rows;
}

function blendPoints(a, b, t) {
    return a.map(function (row, i) {
        return {
            x: row.x + (b[i].x - row.x) * t,
            y: row.y + (b[i].y - row.y) * t,
            z: row.z + (b[i].z - row.z) * t
        };
    });
}

function regression(rows) {
    let sx = 0;
    let sy = 0;
    let sxx = 0;
    let sxy = 0;
    let syy = 0;
    rows.forEach(function (row) {
        sx += row.x;
        sy += row.y;
        sxx += row.x * row.x;
        sxy += row.x * row.y;
        syy += row.y * row.y;
    });
    const n = rows.length;
    const den = n * sxx - sx * sx;
    const slope = den === 0 ? 0 : (n * sxy - sx * sy) / den;
    const intercept = (sy - slope * sx) / n;
    const corDen = Math.sqrt((n * sxx - sx * sx) * (n * syy - sy * sy));
    return { slope: slope, intercept: intercept, r: corDen === 0 ? 0 : (n * sxy - sx * sy) / corDen };
}

function dot(THREE) {
    const canvas = document.createElement('canvas');
    canvas.width = 32;
    canvas.height = 32;
    const ctx = canvas.getContext('2d');
    const grad = ctx.createRadialGradient(16, 16, 1, 16, 16, 15);
    grad.addColorStop(0, 'rgba(255,255,255,1)');
    grad.addColorStop(0.65, 'rgba(255,255,255,0.9)');
    grad.addColorStop(1, 'rgba(255,255,255,0)');
    ctx.fillStyle = grad;
    ctx.fillRect(0, 0, 32, 32);
    return new THREE.CanvasTexture(canvas);
}
