// Hazre Tours: interakcije i animacije. Bez biblioteka; sve radi i bez JS-a (forme, linkovi, sadržaj).
(function () {
  'use strict';
  const $ = (s, el = document) => el.querySelector(s);
  const $$ = (s, el = document) => [...el.querySelectorAll(s)];
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const fine = matchMedia('(pointer: fine)').matches;
  const store = {
    get: k => { try { return localStorage.getItem(k); } catch (e) { return null; } },
    set: (k, v) => { try { localStorage.setItem(k, v); } catch (e) { /* privatni prozor */ } },
  };
  const esc = s => String(s).replace(/[&<>"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' })[c]);

  /* ---------- Zaglavlje: staklo pri skrolu, mobilni meni ---------- */
  const head = $('.site-head');
  const onScroll = () => head && head.classList.toggle('scrolled', scrollY > 10);
  onScroll();
  addEventListener('scroll', onScroll, { passive: true });

  const nav = $('#main-nav');
  const toggle = $('.menu-toggle');
  const closeNav = () => { if (nav) { nav.classList.remove('open'); toggle.setAttribute('aria-expanded', 'false'); document.body.style.overflow = ''; } };
  toggle && toggle.addEventListener('click', () => {
    const open = nav.classList.toggle('open');
    toggle.setAttribute('aria-expanded', String(open));
    document.body.style.overflow = open ? 'hidden' : '';
  });
  nav && $$('a', nav).forEach(a => a.addEventListener('click', closeNav));

  /* ---------- Otkrivanje pri skrolanju ---------- */
  const reveals = $$('[data-reveal]');
  if ('IntersectionObserver' in window && !reduce) {
    const io = new IntersectionObserver(entries => entries.forEach(e => {
      if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
    }), { rootMargin: '0px 0px -8% 0px', threshold: .08 });
    // susjedi u istoj mreži kreću jedan za drugim
    reveals.forEach(el => {
      const sibs = [...el.parentElement.children].filter(c => c.hasAttribute('data-reveal'));
      const i = sibs.indexOf(el);
      if (i > 0 && !el.style.getPropertyValue('--d')) el.style.setProperty('--d', Math.min(i, 6) * 0.07 + 's');
      io.observe(el);
    });
  } else {
    reveals.forEach(el => el.classList.add('in'));
  }

  /* ---------- Brojači ---------- */
  const counters = $$('[data-count]');
  if (counters.length && 'IntersectionObserver' in window && !reduce) {
    const co = new IntersectionObserver(entries => entries.forEach(e => {
      if (!e.isIntersecting) return;
      co.unobserve(e.target);
      const el = e.target, end = parseInt(el.dataset.count, 10) || 0, t0 = performance.now(), dur = 1400;
      const step = now => {
        const p = Math.min(1, (now - t0) / dur);
        el.textContent = Math.round(end * (1 - Math.pow(1 - p, 3)));
        if (p < 1) requestAnimationFrame(step);
      };
      requestAnimationFrame(step);
    }), { threshold: .5 });
    counters.forEach(c => co.observe(c));
  }

  /* ---------- 3D nagib kartica i svjetlo koje prati miš ---------- */
  if (fine && !reduce) {
    $$('[data-tilt]').forEach(card => {
      let raf = 0;
      card.addEventListener('pointermove', e => {
        const r = card.getBoundingClientRect();
        const x = (e.clientX - r.left) / r.width, y = (e.clientY - r.top) / r.height;
        cancelAnimationFrame(raf);
        raf = requestAnimationFrame(() => {
          card.style.setProperty('--mx', x * 100 + '%');
          card.style.setProperty('--my', y * 100 + '%');
          card.style.transform = `perspective(900px) rotateX(${(0.5 - y) * 6}deg) rotateY(${(x - 0.5) * 8}deg) translateY(-4px)`;
        });
      });
      card.addEventListener('pointerleave', () => { cancelAnimationFrame(raf); card.style.transform = ''; });
    });
  }

  /* ---------- Odbrojavanje (akcije) ---------- */
  $$('.countdown[data-until]').forEach(cd => {
    const end = new Date(cd.dataset.until).getTime();
    const parts = { d: $('[data-cd=d]', cd), h: $('[data-cd=h]', cd), m: $('[data-cd=m]', cd), s: $('[data-cd=s]', cd) };
    const tick = () => {
      let t = Math.max(0, Math.floor((end - Date.now()) / 1000));
      const d = Math.floor(t / 86400); t -= d * 86400;
      const h = Math.floor(t / 3600); t -= h * 3600;
      const m = Math.floor(t / 60); const s = t - m * 60;
      parts.d.textContent = d; parts.h.textContent = String(h).padStart(2, '0');
      parts.m.textContent = String(m).padStart(2, '0'); parts.s.textContent = String(s).padStart(2, '0');
    };
    tick(); setInterval(tick, 1000);
  });

  /* ---------- Ploča polazaka: sat i "split-flap" slova ---------- */
  $$('[data-clock]').forEach(el => {
    const tick = () => { el.textContent = new Date().toLocaleTimeString('bs-BA', { hour: '2-digit', minute: '2-digit', second: '2-digit' }); };
    tick(); setInterval(tick, 1000);
  });
  const CH = 'ABCDEFGHIJKLMNOPRSTUVZČĆŠŽĐ0123456789.-';
  const flip = el => {
    const target = el.dataset.flap || el.textContent;
    let frame = 0;
    const total = 14 + Math.floor(Math.random() * 8);
    const run = () => {
      frame++;
      el.textContent = [...target].map((c, i) => {
        if (c === ' ' || frame > total * (i + 1) / target.length + 4) return c;
        return CH[Math.floor(Math.random() * CH.length)];
      }).join('');
      if (frame < total + 6) setTimeout(run, 38); else el.textContent = target;
    };
    run();
  };
  const boards = $$('.board');
  if (!reduce && 'IntersectionObserver' in window) {
    const bo = new IntersectionObserver(entries => entries.forEach(e => {
      if (!e.isIntersecting) return;
      bo.unobserve(e.target);
      $$('.flap', e.target).forEach((f, i) => setTimeout(() => flip(f), i * 60));
    }), { threshold: .2 });
    boards.forEach(b => bo.observe(b));
  }

  /* ---------- Mapa ruta u heroju (canvas): Srebrenik → destinacije, stvarne koordinate ---------- */
  const cv = $('#routes');
  const mapData = $('#map-data');
  if (cv && mapData) {
    let dests = [];
    try { dests = JSON.parse(mapData.textContent || '[]'); } catch (e) { dests = []; }
    const HOME = { lat: 44.7065, lng: 18.4893, dest: 'Srebrenik' };
    const ctx = cv.getContext('2d');
    let W = 0, H = 0, dpr = 1, t = 0, mx = 0.5, my = 0.5, running = true;
    const stars = Array.from({ length: 140 }, () => ({ x: Math.random(), y: Math.random(), r: Math.random() * 1.2 + .2, p: Math.random() * 6 }));
    // projekcija: okvir oko svih tačaka, Srebrenik desno od teksta na velikim ekranima
    const pts = [HOME, ...dests];
    const lats = pts.map(p => p.lat), lngs = pts.map(p => p.lng);
    const box = { minLat: Math.min(...lats) - 1.2, maxLat: Math.max(...lats) + 1.2, minLng: Math.min(...lngs) - 1.5, maxLng: Math.max(...lngs) + 1.5 };
    const proj = p => {
      const wide = W > 900;
      const left = wide ? Math.max(W * 0.56, 700) : W * 0.06, right = W * 0.94, top = H * (wide ? 0.16 : 0.1), bottom = H * (wide ? 0.72 : 0.42);
      const kx = Math.cos(44.7 * Math.PI / 180);
      const spanX = (box.maxLng - box.minLng) * kx, spanY = box.maxLat - box.minLat;
      const s = Math.min((right - left) / spanX, (bottom - top) / spanY);
      const ox = left + ((right - left) - spanX * s) / 2, oy = top + ((bottom - top) - spanY * s) / 2;
      return { x: ox + (p.lng - box.minLng) * kx * s + (mx - .5) * 18, y: oy + (box.maxLat - p.lat) * s + (my - .5) * 12 };
    };
    const resize = () => {
      dpr = Math.min(2, devicePixelRatio || 1);
      const r = cv.getBoundingClientRect(); W = r.width; H = r.height;
      cv.width = W * dpr; cv.height = H * dpr; ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    };
    const draw = () => {
      ctx.clearRect(0, 0, W, H);
      // zvijezde
      stars.forEach(s => {
        const a = .25 + .35 * Math.sin(t / 50 + s.p);
        ctx.fillStyle = `rgba(200,230,250,${a})`;
        ctx.beginPath(); ctx.arc(s.x * W + (mx - .5) * 8 * s.r, s.y * H + (my - .5) * 8 * s.r, s.r, 0, 7); ctx.fill();
      });
      // mreža meridijana i paralela (svaki stepen)
      ctx.strokeStyle = 'rgba(127,221,251,.06)'; ctx.lineWidth = 1;
      for (let lat = Math.ceil(box.minLat); lat <= box.maxLat; lat++) {
        const a = proj({ lat, lng: box.minLng }), b = proj({ lat, lng: box.maxLng });
        ctx.beginPath(); ctx.moveTo(a.x, a.y); ctx.lineTo(b.x, b.y); ctx.stroke();
      }
      for (let lng = Math.ceil(box.minLng); lng <= box.maxLng; lng++) {
        const a = proj({ lat: box.minLat, lng }), b = proj({ lat: box.maxLat, lng });
        ctx.beginPath(); ctx.moveTo(a.x, a.y); ctx.lineTo(b.x, b.y); ctx.stroke();
      }
      const h = proj(HOME);
      const labels = [{ x: h.x, y: h.y }];
      dests.forEach((d, i) => {
        const p = proj(d);
        const mxp = (h.x + p.x) / 2, myp = Math.min(h.y, p.y) - Math.hypot(p.x - h.x, p.y - h.y) * 0.32;
        // ruta
        const g = ctx.createLinearGradient(h.x, h.y, p.x, p.y);
        g.addColorStop(0, 'rgba(255,154,31,.75)'); g.addColorStop(1, 'rgba(51,198,244,.75)');
        ctx.strokeStyle = g; ctx.lineWidth = 1.4; ctx.setLineDash([3, 6]); ctx.lineDashOffset = -t * 0.4;
        ctx.beginPath(); ctx.moveTo(h.x, h.y); ctx.quadraticCurveTo(mxp, myp, p.x, p.y); ctx.stroke();
        ctx.setLineDash([]);
        // autobus koji putuje rutom
        const k = ((t / 260) + i / dests.length) % 1;
        const bx = (1 - k) ** 2 * h.x + 2 * (1 - k) * k * mxp + k * k * p.x;
        const by = (1 - k) ** 2 * h.y + 2 * (1 - k) * k * myp + k * k * p.y;
        const glow = ctx.createRadialGradient(bx, by, 0, bx, by, 10);
        glow.addColorStop(0, 'rgba(255,200,120,.95)'); glow.addColorStop(1, 'rgba(255,154,31,0)');
        ctx.fillStyle = glow; ctx.beginPath(); ctx.arc(bx, by, 10, 0, 7); ctx.fill();
        // destinacija
        const pulse = 4 + 3 * Math.sin(t / 30 + i);
        ctx.strokeStyle = 'rgba(51,198,244,.35)'; ctx.beginPath(); ctx.arc(p.x, p.y, 6 + pulse, 0, 7); ctx.stroke();
        ctx.fillStyle = '#7FDDFB'; ctx.beginPath(); ctx.arc(p.x, p.y, 3.5, 0, 7); ctx.fill();
        // natpis samo ako ne prekriva susjedni grad (Graz, Maribor i Klagenfurt su blizu)
        if (W > 520 && labels.every(l => Math.abs(l.x - p.x) > 70 || Math.abs(l.y - p.y) > 26)) {
          labels.push(p);
          ctx.font = '600 11px "JetBrains Mono", monospace';
          ctx.fillStyle = 'rgba(233,242,248,.9)';
          ctx.fillText(d.dest.toUpperCase(), p.x + 10, p.y - 4);
          ctx.fillStyle = 'rgba(146,169,186,.9)';
          ctx.fillText(d.km + ' km', p.x + 10, p.y + 10);
        }
      });
      // Srebrenik
      const hp = 8 + 4 * Math.sin(t / 20);
      ctx.fillStyle = 'rgba(255,154,31,.18)'; ctx.beginPath(); ctx.arc(h.x, h.y, 10 + hp, 0, 7); ctx.fill();
      ctx.fillStyle = '#FF9A1F'; ctx.beginPath(); ctx.arc(h.x, h.y, 5, 0, 7); ctx.fill();
      if (W > 520) { ctx.font = '700 11px "JetBrains Mono", monospace'; ctx.fillStyle = '#FFB547'; ctx.fillText('SREBRENIK', h.x + 12, h.y + 4); }
    };
    const loop = () => { if (running) { t++; draw(); } requestAnimationFrame(loop); };
    resize(); addEventListener('resize', resize);
    if (fine) addEventListener('pointermove', e => { mx = e.clientX / innerWidth; my = e.clientY / innerHeight; }, { passive: true });
    if (reduce) { t = 120; draw(); }
    else {
      new IntersectionObserver(([e]) => { running = e.isIntersecting; }).observe(cv);
      document.addEventListener('visibilitychange', () => { running = !document.hidden; });
      loop();
    }
  }

  /* ---------- Pretraga i filter aranžmana (početna) ---------- */
  const grid = $('#grid');
  if (grid) {
    const cards = $$('.card', grid);
    const note = $('#result-note');
    const empty = $('#empty');
    const st = { type: 'sve', q: '', month: '', from: '' };
    const apply = () => {
      const q = st.q.trim().toLowerCase();
      let shown = 0;
      cards.forEach(c => {
        const ok = (st.type === 'sve' || c.dataset.type === st.type)
          && (!q || c.dataset.search.includes(q) || (c.dataset.cats || '').includes(q))
          && (!st.month || c.dataset.months.split('|').includes(st.month))
          && (!st.from || c.dataset.from.split('|').includes(st.from));
        c.hidden = !ok;
        if (ok) { shown++; c.classList.add('in'); }
      });
      empty.hidden = shown > 0;
      const filtered = q || st.month || st.from;
      note.hidden = !filtered;
      if (filtered) note.innerHTML = 'Pronađeno: <b>' + shown + '</b> ' + (shown === 1 ? 'aranžman' : 'aranžmana') + '. <button type="button" id="clear">Poništi pretragu</button>';
    };
    const setType = type => {
      st.type = type;
      $$('.chip[data-type]').forEach(c => c.setAttribute('aria-pressed', String(c.dataset.type === type)));
      const sel = $('#s-type'); if (sel) sel.value = type;
      apply();
    };
    $$('.chip[data-type]').forEach(c => c.addEventListener('click', () => setType(c.dataset.type)));
    const finder = $('#finder');
    finder && finder.addEventListener('submit', e => {
      e.preventDefault();
      st.q = $('#s-q').value; st.month = $('#s-month').value; st.from = $('#s-from').value;
      setType($('#s-type').value);
      $('#aranzmani').scrollIntoView();
    });
    note.addEventListener('click', e => {
      if (e.target.id !== 'clear') return;
      Object.assign(st, { q: '', month: '', from: '' });
      finder && finder.reset();
      setType('sve');
    });
    const vrsta = new URLSearchParams(location.search).get('vrsta');
    if (vrsta) setType(vrsta);
  }

  /* ---------- Pretraga preko cijelog ekrana (Ctrl+K) ---------- */
  const ov = $('#search-ov');
  if (ov) {
    let data = [];
    try { data = JSON.parse(($('#search-data') || {}).textContent || '[]'); } catch (e) { data = []; }
    const input = $('#ov-q'), list = $('#ov-res');
    let act = 0, lastFocus = null;
    const render = () => {
      const q = input.value.trim().toLowerCase();
      const words = q.split(/\s+/).filter(Boolean);
      const rows = data.filter(d => words.every(w => d.q.includes(w))).slice(0, 8);
      act = 0;
      list.innerHTML = rows.length ? rows.map((d, i) => `<li><a href="${esc(d.url)}" class="${i === 0 ? 'act' : ''}">${d.img ? `<img src="${esc(d.img)}" alt="" class="${d.flyer ? 'flyer' : ''}">` : '<span class="ph"></span>'}<span><b>${esc(d.title)}</b><small>${esc(d.date)}${d.km ? ' · ' + d.km + ' km' : ''}</small></span><span class="pr">${d.from ? 'od ' : ''}${d.price} KM</span></a></li>`).join('')
        : '<li class="none">Nema putovanja za „' + esc(q) + '“.</li>';
    };
    const open = () => { lastFocus = document.activeElement; ov.hidden = false; document.body.style.overflow = 'hidden'; input.value = ''; render(); setTimeout(() => input.focus(), 30); };
    const close = () => { ov.hidden = true; document.body.style.overflow = ''; lastFocus && lastFocus.focus(); };
    $$('[data-open-search]').forEach(b => b.addEventListener('click', open));
    addEventListener('keydown', e => {
      if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') { e.preventDefault(); ov.hidden ? open() : close(); }
      if (ov.hidden) return;
      const links = $$('a', list);
      if (e.key === 'Escape') close();
      if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
        e.preventDefault();
        act = (act + (e.key === 'ArrowDown' ? 1 : -1) + links.length) % Math.max(1, links.length);
        links.forEach((l, i) => l.classList.toggle('act', i === act));
        links[act] && links[act].scrollIntoView({ block: 'nearest' });
      }
      if (e.key === 'Enter' && links[act] && document.activeElement === input) { e.preventDefault(); location.href = links[act].href; }
    });
    input.addEventListener('input', render);
    ov.addEventListener('click', e => { if (e.target === ov) close(); });
  }

  /* ---------- Plutajući kontakt ---------- */
  const fab = $('[data-fab]');
  if (fab) {
    const btn = $('.fab-main', fab);
    btn.addEventListener('click', () => { const o = fab.classList.toggle('open'); btn.setAttribute('aria-expanded', String(o)); });
    document.addEventListener('click', e => { if (!fab.contains(e.target)) { fab.classList.remove('open'); btn.setAttribute('aria-expanded', 'false'); } });
  }

  /* ---------- Kartice (tabovi) na stranici aranžmana ---------- */
  const tabs = $('.tabs');
  if (tabs) {
    const btns = $$('[role=tab]', tabs), ink = $('.tab-ink', tabs);
    const panels = btns.map(b => document.getElementById(b.getAttribute('aria-controls')));
    const select = (i, focus) => {
      btns.forEach((b, j) => { b.setAttribute('aria-selected', String(i === j)); b.tabIndex = i === j ? 0 : -1; panels[j].hidden = i !== j; });
      ink.style.width = btns[i].offsetWidth + 'px';
      ink.style.transform = `translateX(${btns[i].offsetLeft}px)`;
      if (focus) btns[i].focus();
    };
    btns.forEach((b, i) => {
      b.addEventListener('click', () => { select(i); if (tabs.getBoundingClientRect().top < 0 || scrollY > tabs.offsetTop) tabs.scrollIntoView({ block: 'start' }); });
      b.addEventListener('keydown', e => {
        if (e.key === 'ArrowRight') select((i + 1) % btns.length, true);
        if (e.key === 'ArrowLeft') select((i - 1 + btns.length) % btns.length, true);
      });
    });
    const hash = location.hash.replace('#', '');
    const start = Math.max(0, panels.findIndex(p => p && p.id === 't-' + hash));
    select(start);
    addEventListener('resize', () => select(btns.findIndex(b => b.getAttribute('aria-selected') === 'true')));
  }

  /* ---------- Paralaksa vrha aranžmana ---------- */
  const par = $('[data-parallax]');
  if (par && !reduce) {
    addEventListener('scroll', () => { if (scrollY < innerHeight) par.style.transform = `translateY(${scrollY * 0.25}px)`; }, { passive: true });
  }

  /* ---------- Traka "Rezerviši" na telefonu ---------- */
  const mb = $('.mobile-book'), book = $('#book'), contact = $('#kontakt');
  if (mb && book && 'IntersectionObserver' in window) {
    const vis = new Map();
    const io = new IntersectionObserver(entries => {
      entries.forEach(e => vis.set(e.target, e.isIntersecting));
      mb.classList.toggle('show', ![...vis.values()].some(Boolean) && scrollY > 300);
    });
    io.observe(book); contact && io.observe(contact);
    addEventListener('scroll', () => { if (scrollY <= 300) mb.classList.remove('show'); }, { passive: true });
  }

  /* ---------- Forme: termini, uzrast djece, slanje bez osvježavanja ---------- */
  let termsMap = {};
  try { termsMap = JSON.parse(($('.form-terms') || {}).textContent || '{}'); } catch (e) { termsMap = {}; }
  $$('form.inq').forEach(form => {
    const tripSel = $('.js-trip', form), termSel = $('.js-term', form);
    const fill = (preferred) => {
      if (!tripSel || !termSel) return;
      const list = termsMap[tripSel.value] || [];
      termSel.innerHTML = list.length ? list.map(t => `<option${t === preferred ? ' selected' : ''}>${esc(t)}</option>`).join('')
        : `<option value="">${tripSel.value ? 'Nema slobodnih termina' : 'Izaberite aranžman'}</option>`;
    };
    tripSel && tripSel.addEventListener('change', () => fill());
    fill();
    const kids = $('.js-kids', form), ages = $('.js-kids-ages', form);
    kids && ages && kids.addEventListener('input', () => { ages.hidden = !(parseInt(kids.value, 10) > 0); });
    form._fill = fill;
  });

  // "Rezerviši" na stranici aranžmana: prenese izabrani termin u formu
  $$('[data-book]').forEach(a => a.addEventListener('click', () => {
    const form = $('#kontakt form.inq');
    if (!form) return;
    const tripSel = $('.js-trip', form);
    if (tripSel) tripSel.value = a.dataset.book;
    const picked = $('.term-pick input:checked');
    form._fill && form._fill(picked ? picked.value : undefined);
    setTimeout(() => { const n = $('[name=name]', form); n && n.focus({ preventScroll: true }); }, 600);
  }));

  $$('[data-msg]').forEach(a => a.addEventListener('click', () => { const m = $('#kontakt textarea'); if (m) m.value = a.dataset.msg; }));

  $$('form.js-form').forEach(form => {
    const msg = $('.form-msg', form);
    form.addEventListener('submit', async e => {
      e.preventDefault();
      $$('.err', form).forEach(el => { el.hidden = true; el.textContent = ''; });
      $$('.has-err', form).forEach(f => f.classList.remove('has-err'));
      const btn = $('button[type=submit]', form);
      btn.disabled = true; btn.classList.add('loading');
      try {
        const res = await fetch(form.action, { method: 'POST', body: new FormData(form), headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await res.json().catch(() => ({}));
        msg.hidden = false;
        msg.classList.toggle('bad', !data.ok);
        if (data.ok) {
          msg.textContent = data.message;
          form.reset();
          form._fill && form._fill();
          if (window.gtag) gtag('event', 'generate_lead', { form_type: form.dataset.type || 'newsletter' });
          if (window.fbq) fbq('track', 'Lead');
        } else {
          msg.textContent = data.error || data.message || (res.status === 429 ? 'Previše pokušaja. Pokušajte ponovo za minut.' : 'Upit nije poslan. Pozovite nas telefonom.');
          let first = null;
          Object.entries(data.fields || {}).forEach(([name, text]) => {
            const el = $('[data-err="' + name + '"]', form);
            if (el) { el.textContent = text; el.hidden = false; const f = el.closest('.field'); f && f.classList.add('has-err'); first = first || f; }
          });
          first && first.scrollIntoView({ block: 'center', behavior: reduce ? 'auto' : 'smooth' });
        }
      } catch (err) {
        msg.hidden = false; msg.classList.add('bad');
        msg.textContent = 'Nema veze s internetom. Pokušajte ponovo ili nas pozovite.';
      } finally {
        btn.disabled = false; btn.classList.remove('loading');
      }
    });
  });

  /* ---------- Kopiranje linka ---------- */
  $$('[data-copy]').forEach(b => b.addEventListener('click', async () => {
    try { await navigator.clipboard.writeText(b.dataset.copy); b.textContent = '✓'; } catch (e) { prompt('Kopirajte link:', b.dataset.copy); }
    setTimeout(() => { b.textContent = '⧉'; }, 1600);
  }));

  /* ---------- Galerija (lightbox) ---------- */
  const groups = {};
  $$('[data-lightbox]').forEach(a => { (groups[a.dataset.lightbox] = groups[a.dataset.lightbox] || []).push(a); });
  Object.values(groups).forEach(items => items.forEach((a, idx) => a.addEventListener('click', e => {
    e.preventDefault();
    let i = idx;
    const lb = document.createElement('div');
    lb.className = 'lightbox'; lb.setAttribute('role', 'dialog'); lb.setAttribute('aria-modal', 'true');
    lb.innerHTML = '<img alt=""><button class="lb-close" aria-label="Zatvori">×</button>' + (items.length > 1 ? '<button class="lb-btn lb-prev" aria-label="Prethodna">‹</button><button class="lb-btn lb-next" aria-label="Sljedeća">›</button>' : '');
    const img = $('img', lb);
    const show = () => { img.src = items[i].href; img.alt = ($('img', items[i]) || {}).alt || ''; };
    const close = () => { lb.remove(); removeEventListener('keydown', key); document.body.style.overflow = ''; a.focus(); };
    const key = ev => { if (ev.key === 'Escape') close(); if (ev.key === 'ArrowRight') { i = (i + 1) % items.length; show(); } if (ev.key === 'ArrowLeft') { i = (i - 1 + items.length) % items.length; show(); } };
    lb.addEventListener('click', ev => {
      if (ev.target.closest('.lb-next')) { i = (i + 1) % items.length; show(); }
      else if (ev.target.closest('.lb-prev')) { i = (i - 1 + items.length) % items.length; show(); }
      else if (ev.target === lb || ev.target.closest('.lb-close')) close();
    });
    addEventListener('keydown', key);
    document.body.appendChild(lb); document.body.style.overflow = 'hidden'; show();
    $('.lb-close', lb).focus();
  })));

  /* ---------- Kolačići i analitika (učitava se tek uz pristanak) ---------- */
  const ga = document.body.dataset.ga, px = document.body.dataset.pixel;
  const consent = $('#consent');
  const loadAnalytics = () => {
    if (ga && !window.gtag) {
      const s = document.createElement('script'); s.async = true; s.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(ga); document.head.appendChild(s);
      window.dataLayer = window.dataLayer || []; window.gtag = function () { dataLayer.push(arguments); };
      gtag('js', new Date()); gtag('config', ga, { anonymize_ip: true });
    }
    if (px && !window.fbq) {
      /* eslint-disable */
      !function (f, b, e, v, n, t, s) { if (f.fbq) return; n = f.fbq = function () { n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments); }; if (!f._fbq) f._fbq = n; n.push = n; n.loaded = !0; n.version = '2.0'; n.queue = []; t = b.createElement(e); t.async = !0; t.src = v; s = b.getElementsByTagName(e)[0]; s.parentNode.insertBefore(t, s); }(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');
      fbq('init', px); fbq('track', 'PageView');
    }
  };
  if (consent) {
    const choice = store.get('hazre-consent');
    if (choice === 'yes') loadAnalytics(); else if (!choice) consent.hidden = false;
    $$('[data-consent]', consent).forEach(b => b.addEventListener('click', () => {
      store.set('hazre-consent', b.dataset.consent); consent.hidden = true;
      if (b.dataset.consent === 'yes') loadAnalytics();
    }));
  }
  $$('[data-consent-open]').forEach(a => a.addEventListener('click', e => { e.preventDefault(); if (consent) consent.hidden = false; }));
})();
