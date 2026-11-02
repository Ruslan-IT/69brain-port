document.documentElement.classList.add('js');

(function () {
    function boot() {
        var header = document.querySelector('[data-header]');
        var toggle = document.querySelector('[data-nav-toggle]');
        var nav = document.querySelector('[data-nav]');
        var dropdown = document.querySelector('[data-dropdown]');
        var dropdownBtn = dropdown ? dropdown.querySelector('button') : null;
        var desktop = window.matchMedia('(min-width: 960px)');

        function setDropdown(open) {
            if (!dropdown || !dropdownBtn) {
                return;
            }
            dropdown.classList.toggle('is-open', open);
            dropdownBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
        }

        function setNav(open) {
            if (!header || !toggle) {
                return;
            }
            header.classList.toggle('is-open', open);
            document.body.classList.toggle('nav-open', open);
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
            toggle.setAttribute('aria-label', open ? 'Закрыть меню' : 'Открыть меню');
            if (!desktop.matches) {
                setDropdown(open);
            }
        }

        if (toggle) {
            toggle.addEventListener('click', function () {
                setNav(toggle.getAttribute('aria-expanded') !== 'true');
            });
        }

        if (dropdownBtn) {
            dropdownBtn.addEventListener('click', function () {
                if (!desktop.matches) {
                    return;
                }
                setDropdown(dropdownBtn.getAttribute('aria-expanded') !== 'true');
            });
        }

        document.addEventListener('click', function (event) {
            if (toggle && (event.target === toggle || toggle.contains(event.target))) {
                return;
            }
            if (dropdown && !dropdown.contains(event.target)) {
                setDropdown(false);
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape') {
                return;
            }
            setDropdown(false);
            setNav(false);
        });

        if (nav) {
            nav.addEventListener('click', function (event) {
                if (event.target.closest('a')) {
                    setNav(false);
                    setDropdown(false);
                }
            });
        }

        function onDesktopChange(event) {
            if (event.matches) {
                setNav(false);
                setDropdown(false);
            }
        }

        if (typeof desktop.addEventListener === 'function') {
            desktop.addEventListener('change', onDesktopChange);
        } else if (typeof desktop.addListener === 'function') {
            desktop.addListener(onDesktopChange);
        }

        function onScroll() {
            if (!header) {
                return;
            }
            header.classList.toggle('is-scrolled', window.scrollY > 8);
        }

        onScroll();
        window.addEventListener('scroll', onScroll, { passive: true });

        var revealNodes = document.querySelectorAll('.reveal');
        var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (reduceMotion || !('IntersectionObserver' in window)) {
            revealNodes.forEach(function (node) {
                node.classList.add('is-in');
            });
        } else {
            var observer = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) {
                        return;
                    }
                    entry.target.classList.add('is-in');
                    observer.unobserve(entry.target);
                });
            }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });

            revealNodes.forEach(function (node) {
                observer.observe(node);
            });
        }

        var tocLinks = document.querySelectorAll('.toc a');
        if (tocLinks.length && 'IntersectionObserver' in window) {
            var currentId = '';
            var byId = {};
            tocLinks.forEach(function (link) {
                var id = (link.getAttribute('href') || '').slice(1);
                if (id) {
                    byId[id] = link;
                }
            });

            var spy = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (!entry.isIntersecting) {
                        return;
                    }
                    if (currentId === entry.target.id) {
                        return;
                    }
                    currentId = entry.target.id;
                    tocLinks.forEach(function (link) {
                        link.classList.remove('is-current');
                    });
                    if (byId[currentId]) {
                        byId[currentId].classList.add('is-current');
                    }
                });
            }, { rootMargin: '-30% 0px -55% 0px', threshold: 0 });

            Object.keys(byId).forEach(function (id) {
                var section = document.getElementById(id);
                if (section) {
                    spy.observe(section);
                }
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
