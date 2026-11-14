/* Mira Pools – interactions. Vanilla JS, no dependencies. */
(() => {
  'use strict';
  const doc = document.documentElement;
  doc.classList.add('js');
  const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const fine = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
  const $ = (s, r = document) => r.querySelector(s);
  const $$ = (s, r = document) => Array.from(r.querySelectorAll(s));

  /* ---- split headings into words for the rise-in effect ---- */
  $$('[data-split]').forEach((el) => {
    let w = 0;
    const walk = (node) => {
      Array.from(node.childNodes).forEach((child) => {
        if (child.nodeType === 3) {
          const parts = child.textContent.split(/(\s+)/);
          const frag = document.createDocumentFragment();
          parts.forEach((p) => {
            if (!p) return;
            if (/^\s+$/.test(p)) { frag.appendChild(document.createTextNode(' ')); return; }
            const outer = document.createElement('span');
            outer.className = 'split-w';
            const inner = document.createElement('span');
            inner.textContent = p;
            inner.style.setProperty('--w', w++);
            outer.appendChild(inner);
            frag.appendChild(outer);
          });
          child.replaceWith(frag);
        } else if (child.nodeType === 1) {
          walk(child);
        }
      });
    };
    walk(el);
    el.setAttribute('data-reveal-split', '');
  });

  /* ---- reveal on scroll ---- */
  const revealTargets = $$('[data-reveal], [data-reveal-split], [data-draw]');
  $$('[data-draw]').forEach((p) => {
    if (p.getTotalLength) p.style.setProperty('--len', Math.ceil(p.getTotalLength()));
  });
  if ('IntersectionObserver' in window && !reduce) {
    // Clipped elements have no visible area, so watch their parent instead.
    const watchers = new Map();
    revealTargets.forEach((el) => {
      const key = el.matches('[data-reveal="clip"]') ? el.parentElement : el;
      if (!watchers.has(key)) watchers.set(key, []);
      watchers.get(key).push(el);
    });
    const io = new IntersectionObserver((entries) => {
      entries.forEach((en) => {
        if (!en.isIntersecting) return;
        watchers.get(en.target).forEach((el) => el.classList.add('is-in'));
        io.unobserve(en.target);
      });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.12 });
    watchers.forEach((_, key) => io.observe(key));
  } else {
    revealTargets.forEach((el) => el.classList.add('is-in'));
  }

  /* ---- header: solid after scroll, hide on scroll down ---- */
  const header = $('[data-header]');
  const depth = $('[data-depth]');
  const depthFill = $('[data-depth-fill]');
  const depthVal = $('[data-depth-value]');
  let lastY = window.scrollY;
  let ticking = false;
  const parallax = reduce ? [] : $$('[data-parallax]');

  const onScroll = () => {
    const y = window.scrollY;
    const max = doc.scrollHeight - window.innerHeight;
    if (header) {
      header.classList.toggle('is-solid', y > 40);
      header.classList.toggle('is-hidden', y > 500 && y > lastY + 4 && !doc.classList.contains('menu-open'));
      if (y < lastY - 4) header.classList.remove('is-hidden');
    }
    if (depth) {
      const p = max > 0 ? Math.min(1, y / max) : 0;
      depth.classList.toggle('is-on', y > window.innerHeight * 0.6);
      depthFill.style.height = (p * 100).toFixed(1) + '%';
      depthVal.textContent = (p * 3).toFixed(1) + ' m';
    }
    parallax.forEach((el) => {
      const r = el.parentElement.getBoundingClientRect();
      if (r.bottom < 0 || r.top > window.innerHeight) return;
      const speed = parseFloat(el.dataset.parallax) || 0.15;
      el.style.transform = `translate3d(0, ${(-r.top * speed).toFixed(1)}px, 0)`;
    });
    lastY = y;
    ticking = false;
  };
  window.addEventListener('scroll', () => {
    if (!ticking) { requestAnimationFrame(onScroll); ticking = true; }
  }, { passive: true });
  onScroll();

  /* ---- mobile menu ---- */
  const toggle = $('[data-menu-toggle]');
  const menu = $('[data-mobile-menu]');
  if (toggle && menu) {
    const setOpen = (open) => {
      doc.classList.toggle('menu-open', open);
      toggle.setAttribute('aria-expanded', String(open));
      if (open) menu.hidden = false;
      else setTimeout(() => { if (!doc.classList.contains('menu-open')) menu.hidden = true; }, 800);
    };
    toggle.addEventListener('click', () => setOpen(!doc.classList.contains('menu-open')));
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') setOpen(false); });
    $$('a', menu).forEach((a) => a.addEventListener('click', () => setOpen(false)));
  }

  /* ---- services list: floating image follows the cursor ---- */
  const list = $('[data-svc-list]');
  const float = $('[data-svc-float]');
  if (list && float && fine && !reduce) {
    const img = $('img', float);
    let x = 0, y = 0, cx = 0, cy = 0, raf = null;
    const loop = () => {
      cx += (x - cx) * 0.16; cy += (y - cy) * 0.16;
      float.style.transform = `translate3d(${cx + 30}px, ${cy - 110}px, 0) rotate(${(x - cx) * 0.04}deg) scale(${float.classList.contains('is-on') ? 1 : 0.6})`;
      raf = requestAnimationFrame(loop);
    };
    $$('a[data-img]', list).forEach((a) => {
      a.addEventListener('mouseenter', () => {
        img.src = a.dataset.img;
        float.classList.add('is-on');
        if (!raf) loop();
      });
    });
    list.addEventListener('mousemove', (e) => { x = e.clientX; y = e.clientY; });
    list.addEventListener('mouseleave', () => {
      float.classList.remove('is-on');
      setTimeout(() => { if (!float.classList.contains('is-on')) { cancelAnimationFrame(raf); raf = null; } }, 400);
    });
  }

  /* ---- featured work track: buttons + drag to scroll ---- */
  const track = $('[data-track]');
  if (track) {
    const step = () => track.querySelector('.work-card').getBoundingClientRect().width + 22;
    $('[data-track-prev]')?.addEventListener('click', () => track.scrollBy({ left: -step(), behavior: 'smooth' }));
    $('[data-track-next]')?.addEventListener('click', () => track.scrollBy({ left: step(), behavior: 'smooth' }));
    if (fine) {
      let down = false, startX = 0, startL = 0, moved = false;
      track.addEventListener('pointerdown', (e) => { down = true; moved = false; startX = e.clientX; startL = track.scrollLeft; });
      window.addEventListener('pointermove', (e) => {
        if (!down) return;
        const dx = e.clientX - startX;
        if (Math.abs(dx) > 5) { moved = true; track.classList.add('is-dragging'); }
        track.scrollLeft = startL - dx;
      });
      window.addEventListener('pointerup', () => {
        down = false;
        setTimeout(() => track.classList.remove('is-dragging'), 0);
      });
      track.addEventListener('click', (e) => { if (moved) e.preventDefault(); }, true);
      track.addEventListener('dragstart', (e) => e.preventDefault());
    }
  }

  /* ---- Kerala coast: place office pins along the drawn coastline ---- */
  const coast = $('[data-coast]');
  if (coast) {
    const path = $('[data-coast-path]', coast);
    const svg = $('svg', coast);
    const place = () => {
      const len = path.getTotalLength();
      const vb = svg.viewBox.baseVal;
      const sx = coast.clientWidth / vb.width, sy = coast.clientHeight / vb.height;
      $$('[data-pin]', coast).forEach((pin) => {
        const pt = path.getPointAtLength(len * parseFloat(pin.dataset.pin));
        pin.style.left = (pt.x * sx) + 'px';
        pin.style.top = (pt.y * sy) + 'px';
      });
    };
    place();
    window.addEventListener('resize', place);
  }

  /* ---- gallery filter + lightbox ---- */
  const grid = $('[data-gallery]');
  if (grid) {
    const items = $$('[data-g-item]', grid);
    const filters = $$('[data-filter]');
    let visible = items;
    const apply = (cat) => {
      filters.forEach((f) => f.setAttribute('aria-pressed', String(f.dataset.filter === cat)));
      items.forEach((it) => it.classList.toggle('is-hidden', cat !== 'all' && it.dataset.cat !== cat));
      visible = items.filter((it) => !it.classList.contains('is-hidden'));
      if (!reduce) {
        visible.forEach((it, i) => {
          it.animate([{ opacity: 0, transform: 'translateY(24px)' }, { opacity: 1, transform: 'none' }], { duration: 600, delay: Math.min(i, 12) * 40, easing: 'cubic-bezier(.22,.8,.24,1)', fill: 'backwards' });
        });
      }
    };
    filters.forEach((f) => f.addEventListener('click', () => {
      apply(f.dataset.filter);
      history.replaceState(null, '', f.dataset.filter === 'all' ? location.pathname : '?c=' + f.dataset.filter);
    }));
    const startCat = new URLSearchParams(location.search).get('c');
    if (startCat && filters.some((f) => f.dataset.filter === startCat)) apply(startCat);

    const lb = $('[data-lightbox]');
    const lbImg = $('img', lb);
    const lbTitle = $('[data-lb-title]', lb);
    const lbMeta = $('[data-lb-meta]', lb);
    let idx = 0, lastFocus = null;
    const show = (i) => {
      idx = (i + visible.length) % visible.length;
      const it = visible[idx];
      lbImg.style.opacity = 0;
      const im = new Image();
      im.onload = () => { lbImg.src = it.dataset.full; lbImg.alt = it.dataset.alt; lbImg.style.opacity = 1; };
      im.src = it.dataset.full;
      lbTitle.textContent = it.dataset.title;
      lbMeta.textContent = [it.dataset.catLabel, it.dataset.loc].filter(Boolean).join(' · ') + `  —  ${idx + 1} / ${visible.length}`;
    };
    const open = (it) => {
      lastFocus = document.activeElement;
      lb.classList.add('is-open'); lb.setAttribute('aria-hidden', 'false');
      doc.style.overflow = 'hidden';
      show(visible.indexOf(it));
      $('[data-lb-close]', lb).focus();
    };
    const close = () => {
      lb.classList.remove('is-open'); lb.setAttribute('aria-hidden', 'true');
      doc.style.overflow = '';
      lastFocus?.focus();
    };
    items.forEach((it) => it.addEventListener('click', () => open(it)));
    $('[data-lb-close]', lb).addEventListener('click', close);
    $('[data-lb-prev]', lb).addEventListener('click', () => show(idx - 1));
    $('[data-lb-next]', lb).addEventListener('click', () => show(idx + 1));
    lb.addEventListener('click', (e) => { if (e.target === lb) close(); });
    document.addEventListener('keydown', (e) => {
      if (!lb.classList.contains('is-open')) return;
      if (e.key === 'Escape') close();
      if (e.key === 'ArrowLeft') show(idx - 1);
      if (e.key === 'ArrowRight') show(idx + 1);
    });
    let tx = 0;
    lb.addEventListener('touchstart', (e) => { tx = e.touches[0].clientX; }, { passive: true });
    lb.addEventListener('touchend', (e) => {
      const dx = e.changedTouches[0].clientX - tx;
      if (Math.abs(dx) > 50) show(idx + (dx < 0 ? 1 : -1));
    });
    // open a specific photo from a link like /gallery#seed03
    if (location.hash) {
      const target = items.find((it) => '#' + it.id === location.hash);
      if (target) setTimeout(() => open(target), 400);
    }
  }

  /* ---- map: load the embed only when asked (keeps the page fast) ---- */
  $$('[data-map]').forEach((box) => {
    const btn = $('button', box);
    btn?.addEventListener('click', () => {
      const f = document.createElement('iframe');
      f.src = box.dataset.map;
      f.title = 'Map showing the Mira Pools head office';
      f.loading = 'lazy';
      f.referrerPolicy = 'no-referrer-when-downgrade';
      box.innerHTML = '';
      box.appendChild(f);
    });
  });
})();
