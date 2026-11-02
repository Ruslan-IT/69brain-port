/**
 * Демонстрация статического расчёта консольной балки.
 * Нагрузка, прогиб и цвета — условные, не результат расчёта.
 */
(function () {
    var root = document.querySelector('[data-analysis]');
    if (!root) {
        return;
    }

    var XS = [108, 210, 312, 414, 520];
    var YT = [168, 158, 150, 146, 142];
    var YB = [332, 292, 258, 232, 214];
    var SPAN = XS[4] - XS[0];
    var DEF_MAX = 44;
    var P_MAX = 12.5;
    var UY_MAX = 6.4;
    var STRESS_MAX = 84;
    var CYCLE = 8000;

    var FACES = [
        [0, 5, 1],
        [1, 5, 6],
        [1, 6, 2],
        [2, 6, 7],
        [2, 7, 3],
        [3, 7, 8],
        [3, 8, 4],
        [4, 8, 9]
    ];

    var STOPS = [
        [0, [231, 241, 248]],
        [0.22, [185, 211, 230]],
        [0.42, [126, 175, 204]],
        [0.58, [77, 134, 176]],
        [0.74, [42, 98, 148]],
        [0.88, [26, 69, 110]],
        [1, [16, 40, 63]]
    ];

    var faces = root.querySelectorAll('.beam-face');
    var nodes = root.querySelectorAll('.beam-node');
    var shaft = root.querySelector('[data-load="shaft"]');
    var head = root.querySelector('[data-load="head"]');
    var label = root.querySelector('[data-load="label"]');
    var loadReadout = root.querySelector('[data-readout="load"]');
    var dispReadout = root.querySelector('[data-readout="disp"]');
    var stressReadout = root.querySelector('[data-readout="stress-max"]');

    var faceStress = FACES.map(function (face) {
        var sum = 0;
        face.forEach(function (index) {
            sum += 1 - stationOf(index) / 4;
        });
        return sum / 3;
    });

    var reduceMedia = window.matchMedia('(prefers-reduced-motion: reduce)');
    var hover = 0;
    var hoverTarget = 0;
    var running = false;
    var handle = null;
    var visible = true;
    var origin = performance.now();
    var pausedAt = 0;

    function stationOf(index) {
        return index < 5 ? index : index - 5;
    }

    function smooth(x) {
        var t = Math.min(1, Math.max(0, x));
        return t * t * (3 - 2 * t);
    }

    function envelope(u) {
        if (u < 0.42) {
            return smooth(u / 0.42);
        }
        if (u < 0.7) {
            return 1;
        }
        return 1 - smooth((u - 0.7) / 0.3);
    }

    function mix(a, b, t) {
        return [
            Math.round(a[0] + (b[0] - a[0]) * t),
            Math.round(a[1] + (b[1] - a[1]) * t),
            Math.round(a[2] + (b[2] - a[2]) * t)
        ];
    }

    function colorAt(amount) {
        var t = Math.min(1, Math.max(0, amount));
        var i;
        for (i = 1; i < STOPS.length; i += 1) {
            if (t <= STOPS[i][0]) {
                var span = STOPS[i][0] - STOPS[i - 1][0];
                var local = span === 0 ? 0 : (t - STOPS[i - 1][0]) / span;
                var rgb = mix(STOPS[i - 1][1], STOPS[i][1], local);
                return 'rgb(' + rgb[0] + ',' + rgb[1] + ',' + rgb[2] + ')';
            }
        }
        var last = STOPS[STOPS.length - 1][1];
        return 'rgb(' + last[0] + ',' + last[1] + ',' + last[2] + ')';
    }

    function point(index, lambda, boost) {
        var station = stationOf(index);
        var x0 = XS[station];
        var y0 = index < 5 ? YT[station] : YB[station];
        var s = (x0 - XS[0]) / SPAN;
        var bend = s * s * (3 - s) / 2;
        var slope = s * (2 - s);
        var scale = lambda * boost;
        var v = DEF_MAX * scale * bend;
        var theta = (1.5 * DEF_MAX / SPAN) * scale * slope;
        var yNa = (YT[station] + YB[station]) / 2;
        return [x0 + (yNa - y0) * theta, y0 + v];
    }

    function fmt(value) {
        return value.toFixed(1);
    }

    function draw(lambda, u, boost, animate) {
        var coords = [];
        var i;
        for (i = 0; i < 10; i += 1) {
            coords[i] = point(i, lambda, boost);
        }

        var shift = 0;
        if (animate && lambda > 0.85) {
            shift = 0.07 * Math.sin(((u - 0.42) / 0.28) * Math.PI);
        }

        faces.forEach(function (face, index) {
            var tri = FACES[index];
            var amount = faceStress[index] * lambda + shift * (faceStress[index] - 0.45);
            amount = Math.min(1, Math.max(0, amount));
            face.setAttribute('fill', colorAt(amount));
            face.setAttribute('points', tri.map(function (vertex) {
                return coords[vertex][0].toFixed(2) + ',' + coords[vertex][1].toFixed(2);
            }).join(' '));
        });

        var travel = Math.min(1, u / 0.62);
        var pulseIn = smooth(Math.min(1, u / 0.1));
        var pulseOut = u < 0.62 ? 1 : Math.max(0, 1 - (u - 0.62) / 0.1);
        var pulse = animate ? pulseIn * pulseOut : 0;
        nodes.forEach(function (node) {
            var index = Number(node.getAttribute('data-index'));
            var xy = coords[index];
            var s = stationOf(index) / 4;
            var glow = pulse * Math.exp(-Math.pow(s - travel, 2) / 0.018);
            node.setAttribute('cx', xy[0].toFixed(2));
            node.setAttribute('cy', xy[1].toFixed(2));
            node.setAttribute('r', (3.2 + glow * 1.7).toFixed(2));
            node.setAttribute('stroke', glow > 0.45 ? '#1a6b8a' : '#0e2a47');
        });

        var tip = coords[4];
        var length = 22 + 26 * lambda;
        var x = tip[0];
        var yTop = tip[1] - 6;
        var yTail = yTop - length;
        shaft.setAttribute('x1', x.toFixed(2));
        shaft.setAttribute('y1', yTail.toFixed(2));
        shaft.setAttribute('x2', x.toFixed(2));
        shaft.setAttribute('y2', yTop.toFixed(2));
        var shown = Math.min(1, Math.max(0, (lambda - 0.05) / 0.28));
        root.querySelector('.beam-load').setAttribute('opacity', shown.toFixed(3));
        shaft.setAttribute('stroke-opacity', shown.toFixed(3));
        head.setAttribute('points', [
            x.toFixed(2) + ',' + (tip[1] - 1).toFixed(2),
            (x - 6).toFixed(2) + ',' + (tip[1] - 13).toFixed(2),
            (x + 6).toFixed(2) + ',' + (tip[1] - 13).toFixed(2)
        ].join(' '));
        head.setAttribute('fill-opacity', shown.toFixed(3));
        label.setAttribute('x', (x - 12).toFixed(2));
        label.setAttribute('y', (yTail + 4).toFixed(2));
        label.setAttribute('fill-opacity', shown.toFixed(3));
        label.textContent = 'P = ' + fmt(P_MAX * lambda) + ' kN';

        if (loadReadout) {
            loadReadout.textContent = fmt(P_MAX * lambda) + ' kN';
        }
        if (dispReadout) {
            dispReadout.textContent = fmt(UY_MAX * lambda) + ' mm';
        }
        if (stressReadout) {
            stressReadout.textContent = fmt(STRESS_MAX * lambda) + ' МПа';
        }
    }

    function schedule(callback) {
        var settled = false;
        var timer = setTimeout(function () {
            if (settled) {
                return;
            }
            settled = true;
            cancelAnimationFrame(raf);
            callback(performance.now());
        }, 32);
        var raf = requestAnimationFrame(function (now) {
            if (settled) {
                return;
            }
            settled = true;
            clearTimeout(timer);
            callback(now);
        });
        return { raf: raf, timer: timer };
    }

    function frame(now) {
        if (!running) {
            return;
        }
        var u = ((now - origin) % CYCLE) / CYCLE;
        hover += (hoverTarget - hover) * 0.08;
        draw(envelope(u), u, 1 + hover * 0.12, true);
        handle = schedule(frame);
    }

    function start() {
        if (running || reduceMedia.matches) {
            return;
        }
        if (pausedAt) {
            origin += performance.now() - pausedAt;
            pausedAt = 0;
        }
        running = true;
        handle = schedule(frame);
    }

    function stop() {
        if (!running) {
            return;
        }
        running = false;
        pausedAt = performance.now();
        if (!handle) {
            return;
        }
        cancelAnimationFrame(handle.raf);
        clearTimeout(handle.timer);
        handle = null;
    }

    root.addEventListener('pointerenter', function () {
        hoverTarget = 1;
    });
    root.addEventListener('pointerleave', function () {
        hoverTarget = 0;
    });

    if ('IntersectionObserver' in window) {
        var observer = new IntersectionObserver(function (entries) {
            if (document.hidden) {
                return;
            }
            visible = entries[0].isIntersecting;
            if (visible) {
                start();
            } else {
                stop();
            }
        }, { threshold: 0.08 });
        observer.observe(root);
    }

    var sawVisible = !document.hidden;
    document.addEventListener('visibilitychange', function () {
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
    });

    function applyMotion(matches) {
        if (matches) {
            stop();
            hover = 0;
            draw(1, 0.5, 1, false);
            return;
        }
        origin = performance.now();
        start();
    }

    if (typeof reduceMedia.addEventListener === 'function') {
        reduceMedia.addEventListener('change', function (event) {
            applyMotion(event.matches);
        });
    }

    applyMotion(reduceMedia.matches);
})();
