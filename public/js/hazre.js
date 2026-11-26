// Hazre Tours: meni, slider, filter aranžmana, pretraga, forme, galerija, kolačići.
// Bez biblioteka; sadržaj, linkovi i forme rade i bez JavaScripta.
(function () {
  'use strict';
  const $ = (s, el = document) => el.querySelector(s);
  const $$ = (s, el = document) => [...el.querySelectorAll(s)];
  const reduce = matchMedia('(prefers-reduced-motion: reduce)').matches;
  const store = {
    get: k => { try { return localStorage.getItem(k); } catch (e) { return null; } },
    set: (k, v) => { try { localStorage.setItem(k, v); } catch (e) { /* privatni prozor */ } },
  };
  const esc = s => String(s).replace(/[&<>"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' })[c]);

  /* ---------- Zaglavlje i mobilni meni ---------- */
  const head = $('.site-head');
  const onScroll = () => head && head.classList.toggle('scrolled', scrollY > 10);
  onScroll();
  addEventListener('scroll', onScroll, { passive: true });

  const nav = $('#main-nav');
  const toggle = $('.menu-toggle');
  const setNav = open => {
    if (!nav) return;
    nav.classList.toggle('open', open);
    toggle && toggle.setAttribute('aria-expanded', String(open));
    document.body.style.overflow = open ? 'hidden' : '';
    document.body.classList.toggle('nav-open', open);
    if (open) { const c = $('.nav-close', nav); c && c.focus(); } else if (toggle) toggle.focus({ preventScroll: true });
  };
  const closeNav = () => { if (nav && nav.classList.contains('open')) setNav(false); };
  toggle && toggle.addEventListener('click', () => setNav(!nav.classList.contains('open')));
  nav && $$('a', nav).forEach(a => a.addEventListener('click', closeNav));
  $('.nav-close') && $('.nav-close').addEventListener('click', closeNav);
  $('.nav-backdrop') && $('.nav-backdrop').addEventListener('click', closeNav);
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeNav(); });
  $$('.drop-toggle').forEach(b => b.addEventListener('click', () => {
    const li = b.closest('.has-drop');
    const open = li.classList.toggle('open');
    b.setAttribute('aria-expanded', String(open));
  }));

  /* ---------- Slider na početnoj ---------- */
  const slider = $('.slider');
  if (slider) {
    const slides = $$('.slide', slider), dots = $$('.dot', slider);
    let i = 0, timer = 0;
    const go = n => {
      i = (n + slides.length) % slides.length;
      slides.forEach((s, j) => { s.classList.toggle('on', j === i); s.setAttribute('aria-hidden', String(j !== i)); });
      dots.forEach((d, j) => d.setAttribute('aria-current', String(j === i)));
    };
    const start = () => { if (!reduce && slides.length > 1) { clearInterval(timer); timer = setInterval(() => go(i + 1), 6000); } };
    dots.forEach((d, j) => d.addEventListener('click', () => { go(j); start(); }));
    $('.sl-prev', slider) && $('.sl-prev', slider).addEventListener('click', () => { go(i - 1); start(); });
    $('.sl-next', slider) && $('.sl-next', slider).addEventListener('click', () => { go(i + 1); start(); });
    slider.addEventListener('mouseenter', () => clearInterval(timer));
    slider.addEventListener('mouseleave', start);
    // prevlačenje prstom na telefonu
    let x0 = null;
    slider.addEventListener('touchstart', e => { x0 = e.touches[0].clientX; }, { passive: true });
    slider.addEventListener('touchend', e => {
      if (x0 === null) return;
      const dx = e.changedTouches[0].clientX - x0;
      if (Math.abs(dx) > 40) { go(i + (dx < 0 ? 1 : -1)); start(); }
      x0 = null;
    });
    go(0); start();
  }

  /* ---------- Odbrojavanje akcije ---------- */
  $$('.countdown[data-until]').forEach(cd => {
    const end = new Date(cd.dataset.until).getTime();
    const p = { d: $('[data-cd=d]', cd), h: $('[data-cd=h]', cd), m: $('[data-cd=m]', cd), s: $('[data-cd=s]', cd) };
    const tick = () => {
      let t = Math.max(0, Math.floor((end - Date.now()) / 1000));
      const d = Math.floor(t / 86400); t -= d * 86400;
      const h = Math.floor(t / 3600); t -= h * 3600;
      const m = Math.floor(t / 60);
      p.d.textContent = d; p.h.textContent = String(h).padStart(2, '0');
      p.m.textContent = String(m).padStart(2, '0'); p.s.textContent = String(t - m * 60).padStart(2, '0');
    };
    tick(); setInterval(tick, 1000);
  });

  /* ---------- Filter i pretraga aranžmana (početna) ---------- */
  const grid = $('#grid');
  if (grid) {
    const cards = $$('.card', grid);
    const note = $('#result-note');
    const empty = $('#empty');
    const panel = $('#filter-panel');
    const st = { type: 'sve', cat: '', q: '', month: '', from: '' };
    const tabs = $$('.cat-tabs .ct');
    const apply = () => {
      const q = st.q.trim().toLowerCase();
      let shown = 0;
      cards.forEach(c => {
        const ok = (st.type === 'sve' || c.dataset.type === st.type)
          && (!q || c.dataset.search.includes(q) || (c.dataset.cats || '').includes(q))
          && (!st.month || c.dataset.months.split('|').includes(st.month))
          && (!st.from || c.dataset.from.split('|').includes(st.from))
          && (!st.cat || (c.dataset.cats || '').split('|').includes(st.cat));
        c.hidden = !ok;
        if (ok) shown++;
      });
      empty.hidden = shown > 0;
      const filtered = q || st.month || st.from;
      // bez filtera početna pokazuje samo izabrane (dva reda); s filterom traži kroz sva putovanja
      grid.classList.toggle('filtering', !!(filtered || st.cat || st.type !== 'sve'));
      note.hidden = !filtered;
      if (filtered) note.innerHTML = 'Pronađeno: <b>' + shown + '</b> ' + (shown === 1 ? 'aranžman' : 'aranžmana') + '. <button type="button" id="clear">Poništi filter</button>';
    };
    const read = () => {
      st.q = ($('#s-q') || {}).value || '';
      st.type = ($('#s-type') || {}).value || 'sve';
      st.month = ($('#s-month') || {}).value || '';
      st.from = ($('#s-from') || {}).value || '';
      apply();
    };
    // Tabovi kategorija: aktivan je jedan; "Jednodnevni izleti" filtrira po vrsti, ostali po kategoriji
    tabs.forEach(b => b.addEventListener('click', () => {
      tabs.forEach(t => t.setAttribute('aria-pressed', String(t === b)));
      st.cat = b.dataset.cat || '';
      st.type = b.dataset.type || 'sve';
      if ($('#s-type')) $('#s-type').value = st.type;
      apply();
    }));
    const fbtn = $('#filter-btn');
    fbtn && fbtn.addEventListener('click', () => {
      const open = panel.hidden;
      panel.hidden = !open;
      fbtn.setAttribute('aria-expanded', String(open));
    });
    $$('#filter-panel select').forEach(s => s.addEventListener('change', read));
    const search = $('#search');
    search && search.addEventListener('submit', e => { e.preventDefault(); read(); $('#aranzmani').scrollIntoView({ behavior: reduce ? 'auto' : 'smooth' }); });
    note.addEventListener('click', e => {
      if (e.target.id !== 'clear') return;
      $$('#filter-panel select').forEach(s => { s.selectedIndex = 0; });
      if ($('#s-q')) $('#s-q').value = '';
      tabs.forEach((t, i) => t.setAttribute('aria-pressed', String(i === 0)));
      st.cat = '';
      read();
    });
    const vrsta = new URLSearchParams(location.search).get('vrsta');
    if (vrsta && $('#s-type')) { $('#s-type').value = vrsta; panel.hidden = false; read(); }
  }

  /* ---------- Pretraga ispod heroja: rezultati dok se kuca ---------- */
  const sbDrop = $('#sb-drop');
  if (sbDrop) {
    let data = [];
    try { data = JSON.parse(($('#search-data') || {}).textContent || '[]'); } catch (e) { data = []; }
    const input = $('#s-q'), list = $('#sb-res'), wrap = $('.sb-wrap');
    let act = -1;
    const links = () => $$('a', list);
    const mark = () => links().forEach((l, i) => { l.classList.toggle('act', i === act); l.setAttribute('aria-selected', String(i === act)); });
    const render = () => {
      const words = input.value.trim().toLowerCase().split(/\s+/).filter(Boolean);
      const rows = data.filter(d => words.every(w => d.q.includes(w))).slice(0, 8);
      act = -1;
      list.innerHTML = rows.length
        ? rows.map(d => `<li><a href="${esc(d.url)}" role="option" aria-selected="false">${d.img ? `<img src="${esc(d.img)}" alt="">` : '<span class="ph"></span>'}<span class="sr-t"><b>${esc(d.title)}</b><small>${esc(d.date)}</small></span><span class="pr">${d.from ? 'od ' : ''}${d.price} KM</span></a></li>`).join('')
        : '<li class="none">Nema putovanja za „' + esc(input.value) + '“.</li>';
    };
    const open = () => { render(); sbDrop.hidden = false; input.setAttribute('aria-expanded', 'true'); };
    const close = () => { sbDrop.hidden = true; input.setAttribute('aria-expanded', 'false'); act = -1; };
    input.addEventListener('focus', open);
    input.addEventListener('input', open);
    input.addEventListener('keydown', e => {
      const ls = links();
      if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
        e.preventDefault();
        if (sbDrop.hidden) open();
        if (!ls.length) return;
        act = e.key === 'ArrowDown' ? (act + 1) % ls.length : (act - 1 + ls.length) % ls.length;
        mark();
        ls[act].scrollIntoView({ block: 'nearest' });
      } else if (e.key === 'Enter' && act >= 0 && ls[act]) {
        e.preventDefault();
        location.href = ls[act].href;
      } else if (e.key === 'Escape') {
        close();
      }
    });
    // Enter bez izabranog rezultata: zatvori listu i filtriraj kartice ispod (radi i forma iznad)
    $('#search').addEventListener('submit', close);
    document.addEventListener('click', e => { if (!wrap.contains(e.target)) close(); });
  }

  /* ---------- Kontakt dugme (poziv, Viber, WhatsApp) ---------- */
  const fab = $('[data-fab]');
  if (fab) {
    const btn = $('.fab-main', fab);
    btn.addEventListener('click', () => { const o = fab.classList.toggle('open'); btn.setAttribute('aria-expanded', String(o)); });
    document.addEventListener('click', e => { if (!fab.contains(e.target)) { fab.classList.remove('open'); btn.setAttribute('aria-expanded', 'false'); } });
  }

  /* ---------- Kartice na stranici aranžmana ---------- */
  const tabs = $('.tabs');
  if (tabs) {
    const btns = $$('[role=tab]', tabs);
    const panels = btns.map(b => document.getElementById(b.getAttribute('aria-controls')));
    const select = (i, focus) => {
      btns.forEach((b, j) => { b.setAttribute('aria-selected', String(i === j)); b.tabIndex = i === j ? 0 : -1; panels[j].hidden = i !== j; });
      if (focus) btns[i].focus();
    };
    btns.forEach((b, i) => {
      b.addEventListener('click', () => select(i));
      b.addEventListener('keydown', e => {
        if (e.key === 'ArrowRight') select((i + 1) % btns.length, true);
        if (e.key === 'ArrowLeft') select((i - 1 + btns.length) % btns.length, true);
      });
    });
    select(0);
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
  }

  /* ---------- Forme: termini, uzrast djece, slanje bez osvježavanja ---------- */
  let termsMap = {};
  try { termsMap = JSON.parse(($('.form-terms') || {}).textContent || '{}'); } catch (e) { termsMap = {}; }
  $$('form.inq').forEach(form => {
    const tripSel = $('.js-trip', form), termSel = $('.js-term', form);
    const fill = preferred => {
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

  $$('[data-book]').forEach(a => a.addEventListener('click', () => {
    const form = $('#kontakt form.inq');
    if (!form) return;
    const tripSel = $('.js-trip', form);
    if (tripSel) tripSel.value = a.dataset.book;
    const picked = $('.term-pick input:checked');
    form._fill && form._fill(picked ? picked.value : undefined);
    setTimeout(() => { const n = $('[name=name]', form); n && n.focus({ preventScroll: true }); }, 500);
  }));

  $$('[data-msg]').forEach(a => a.addEventListener('click', () => { const m = $('#kontakt textarea'); if (m) m.value = a.dataset.msg; }));

  $$('form.js-form').forEach(form => {
    const msg = $('.form-msg', form);
    form.addEventListener('submit', async e => {
      e.preventDefault();
      $$('.err', form).forEach(el => { el.hidden = true; el.textContent = ''; });
      $$('.has-err', form).forEach(f => f.classList.remove('has-err'));
      const btn = $('button[type=submit]', form);
      btn.disabled = true;
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
          first && first.scrollIntoView({ block: 'center' });
        }
      } catch (err) {
        msg.hidden = false; msg.classList.add('bad');
        msg.textContent = 'Nema veze s internetom. Pokušajte ponovo ili nas pozovite.';
      } finally {
        btn.disabled = false;
      }
    });
  });

  /* ---------- Kopiranje linka ---------- */
  $$('[data-copy]').forEach(b => b.addEventListener('click', async () => {
    const old = b.textContent;
    try { await navigator.clipboard.writeText(b.dataset.copy); b.textContent = 'Kopirano'; } catch (e) { prompt('Kopirajte link:', b.dataset.copy); }
    setTimeout(() => { b.textContent = old; }, 1600);
  }));

  /* ---------- Galerija ---------- */
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

  /* ---------- Kolačići i analitika (tek uz pristanak) ---------- */
  const ga = document.body.dataset.ga, px = document.body.dataset.pixel;
  const consent = $('#consent');
  // izbor se čuva kao {a: analitika, m: marketing}; stari zapisi "yes"/"no" i dalje važe
  const readChoice = () => {
    const v = store.get('hazre-consent');
    if (v === 'yes') return { a: true, m: true };
    if (v === 'no') return { a: false, m: false };
    try { const o = JSON.parse(v || 'null'); return o && typeof o === 'object' ? o : null; } catch (e) { return null; }
  };
  const loadAnalytics = (c = { a: true, m: true }) => {
    if (c.a && ga && !window.gtag) {
      const s = document.createElement('script'); s.async = true; s.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(ga); document.head.appendChild(s);
      window.dataLayer = window.dataLayer || []; window.gtag = function () { dataLayer.push(arguments); };
      gtag('js', new Date()); gtag('config', ga, { anonymize_ip: true });
    }
    if (c.m && px && !window.fbq) {
      /* eslint-disable */
      !function (f, b, e, v, n, t, s) { if (f.fbq) return; n = f.fbq = function () { n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments); }; if (!f._fbq) f._fbq = n; n.push = n; n.loaded = !0; n.version = '2.0'; n.queue = []; t = b.createElement(e); t.async = !0; t.src = v; s = b.getElementsByTagName(e)[0]; s.parentNode.insertBefore(t, s); }(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');
      fbq('init', px); fbq('track', 'PageView');
    }
  };
  if (consent) {
    const prefs = $('#cz-prefs', consent), toggle = $('[data-cz-toggle]', consent);
    const boxes = $$('[data-cz]', consent);
    const saveBtn = $('[data-consent="save"]', consent);
    const show = () => {
      const c = readChoice() || { a: false, m: false };
      boxes.forEach(b => { b.checked = !!c[b.dataset.cz]; });
      consent.hidden = false;
      requestAnimationFrame(() => consent.classList.add('in'));
    };
    const hide = () => { consent.classList.remove('in'); setTimeout(() => { consent.hidden = true; }, 300); };
    const setPrefs = open => {
      prefs.hidden = !open;
      toggle.setAttribute('aria-expanded', String(open));
      toggle.textContent = open ? 'Sakrij postavke' : 'Postavke';
      saveBtn.hidden = !open;
      consent.classList.toggle('has-prefs', open);
    };
    const choice = readChoice();
    if (choice) loadAnalytics(choice); else setTimeout(show, 900);
    toggle.addEventListener('click', () => setPrefs(prefs.hidden));
    $$('[data-consent]', consent).forEach(b => b.addEventListener('click', () => {
      const kind = b.dataset.consent;
      const c = kind === 'yes' ? { a: true, m: true } : kind === 'no' ? { a: false, m: false }
        : Object.fromEntries(boxes.map(x => [x.dataset.cz, x.checked]));
      store.set('hazre-consent', JSON.stringify(c));
      hide();
      loadAnalytics(c);
    }));
  }
  $$('[data-consent-open]').forEach(a => a.addEventListener('click', e => {
    e.preventDefault();
    if (!consent) return;
    const c = readChoice() || { a: false, m: false };
    $$('[data-cz]', consent).forEach(b => { b.checked = !!c[b.dataset.cz]; });
    consent.hidden = false;
    requestAnimationFrame(() => consent.classList.add('in'));
    const t = $('[data-cz-toggle]', consent);
    if (t && $('#cz-prefs').hidden) t.click();
  }));
})();

// Natpis u podnožju: slova izranjaju kad se dođe do dna stranice
(() => {
  const mark = document.querySelector('.foot-mark');
  if (!mark) return;
  if (!('IntersectionObserver' in window)) { mark.classList.add('in'); return; }
  const io = new IntersectionObserver((entries) => {
    entries.forEach((e) => { if (e.isIntersecting) { mark.classList.add('in'); io.disconnect(); } });
  }, { threshold: 0.3 });
  io.observe(mark);
})();
// rezerva: ako posmatrač ne javi (npr. pozadinski prozor), provjeri pri skrolanju
(() => {
  const mark = document.querySelector('.foot-mark');
  if (!mark) return;
  const check = () => {
    if (mark.getBoundingClientRect().top < innerHeight * 0.92) { mark.classList.add('in'); removeEventListener('scroll', check); }
  };
  addEventListener('scroll', check, { passive: true });
  check();
})();

// Padajuće liste u stilu sajta (na računaru; na telefonu ostaje izvorni birač).
// Prava <select> lista ostaje ispod i šalje vrijednost; ova samo prikazuje i bira.
(() => {
  if (!matchMedia('(hover: hover) and (pointer: fine)').matches) return;
  const proto = HTMLSelectElement.prototype;
  const valueDesc = Object.getOwnPropertyDescriptor(proto, 'value');
  const indexDesc = Object.getOwnPropertyDescriptor(proto, 'selectedIndex');
  const esc = s => String(s).replace(/[&<>"]/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]));
  let open = null;
  const closeAll = () => {
    if (!open) return;
    open.classList.remove('open');
    open.querySelector('.dd-btn').setAttribute('aria-expanded', 'false');
    open = null;
  };
  document.addEventListener('click', e => { if (open && !open.contains(e.target)) closeAll(); });
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && open) { const b = open.querySelector('.dd-btn'); closeAll(); b.focus(); }
  });

  const enhance = sel => {
    if (sel.dataset.dd || sel.multiple) return;
    sel.dataset.dd = '1';
    const wrap = document.createElement('div');
    wrap.className = 'dd';
    sel.parentNode.insertBefore(wrap, sel);
    wrap.appendChild(sel);
    sel.classList.add('dd-native');
    sel.tabIndex = -1;
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.className = 'dd-btn';
    btn.setAttribute('aria-haspopup', 'listbox');
    btn.setAttribute('aria-expanded', 'false');
    const lbl = sel.id ? document.querySelector('label[for="' + sel.id + '"]') : sel.closest('label');
    if (lbl) {
      if (lbl.htmlFor) { lbl.htmlFor = ''; lbl.addEventListener('click', () => btn.focus()); }
      btn.setAttribute('aria-label', (lbl.querySelector('span') || lbl).textContent.trim());
    }
    const list = document.createElement('ul');
    list.className = 'dd-list';
    list.setAttribute('role', 'listbox');
    wrap.append(btn, list);
    let act = -1;

    const sync = () => {
      const o = sel.options[sel.selectedIndex];
      btn.textContent = o ? o.textContent : '';
      btn.classList.toggle('placeholder', !o || o.value === '');
      [...list.children].forEach((li, i) => li.setAttribute('aria-selected', String(i === sel.selectedIndex)));
      btn.disabled = sel.disabled;
    };
    const render = () => {
      list.innerHTML = [...sel.options].map((o, i) =>
        '<li role="option" data-i="' + i + '" aria-selected="' + o.selected + '"' + (o.disabled ? ' aria-disabled="true"' : '') + '>' + esc(o.textContent) + '</li>'
      ).join('');
      sync();
    };
    const mark = i => {
      act = i;
      [...list.children].forEach((li, j) => li.classList.toggle('act', j === i));
      if (list.children[i]) list.children[i].scrollIntoView({ block: 'nearest' });
    };
    const choose = i => {
      const o = sel.options[i];
      if (!o || o.disabled) return;
      indexDesc.set.call(sel, i);
      sync();
      sel.dispatchEvent(new Event('input', { bubbles: true }));
      sel.dispatchEvent(new Event('change', { bubbles: true }));
      closeAll();
      btn.focus();
    };
    const toggle = () => {
      if (wrap.classList.contains('open')) { closeAll(); return; }
      closeAll();
      wrap.classList.add('open');
      btn.setAttribute('aria-expanded', 'true');
      open = wrap;
      // otvori prema gore ako ispod nema mjesta
      const r = btn.getBoundingClientRect();
      wrap.classList.toggle('up', innerHeight - r.bottom < 260 && r.top > innerHeight - r.bottom);
      mark(sel.selectedIndex);
    };

    btn.addEventListener('click', toggle);
    btn.addEventListener('keydown', e => {
      const n = sel.options.length;
      if (!n) return;
      const step = d => { let i = act < 0 ? 0 : act; let k = 0; do { i = (i + d + n) % n; k++; } while (sel.options[i].disabled && k < n); return i; };
      if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
        e.preventDefault();
        if (!wrap.classList.contains('open')) { toggle(); return; }
        mark(step(e.key === 'ArrowDown' ? 1 : -1));
      } else if ((e.key === 'Enter' || e.key === ' ') && wrap.classList.contains('open')) {
        e.preventDefault();
        choose(act);
      }
    });
    list.addEventListener('click', e => { const li = e.target.closest('li'); if (li) choose(+li.dataset.i); });
    list.addEventListener('mousemove', e => { const li = e.target.closest('li'); if (li && +li.dataset.i !== act) mark(+li.dataset.i); });
    sel.addEventListener('change', sync);
    new MutationObserver(render).observe(sel, { childList: true, subtree: true, attributes: true, attributeFilter: ['disabled'] });
    // skripta koja postavi vrijednost bez događaja (reset filtera, "Rezerviši") i dalje ažurira prikaz
    Object.defineProperty(sel, 'value', { configurable: true, get() { return valueDesc.get.call(this); }, set(v) { valueDesc.set.call(this, v); sync(); } });
    Object.defineProperty(sel, 'selectedIndex', { configurable: true, get() { return indexDesc.get.call(this); }, set(v) { indexDesc.set.call(this, v); sync(); } });
    if (sel.form) sel.form.addEventListener('reset', () => setTimeout(sync));
    render();
  };

  document.querySelectorAll('.field select, .filter-panel select').forEach(enhance);
})();

// Sekcije koje se animiraju kad dođu na ekran (npr. autobus na stranici O nama)
(() => {
  const els = [...document.querySelectorAll('.js-inview')];
  if (!els.length) return;
  const check = () => {
    els.forEach(el => { if (!el.classList.contains('in') && el.getBoundingClientRect().top < innerHeight * 0.8) el.classList.add('in'); });
    if (els.every(el => el.classList.contains('in'))) removeEventListener('scroll', check);
  };
  addEventListener('scroll', check, { passive: true });
  check();
})();

// "Na vrh": uvijek na sam početak stranice (zaglavlje je ljepljivo pa link #top pomjeri samo malo)
document.querySelectorAll('.to-top, a[href="#top"]').forEach(a => a.addEventListener('click', e => {
  e.preventDefault();
  window.scrollTo({ top: 0, behavior: matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth' });
  history.replaceState(null, '', location.pathname + location.search);
}));

/* Na telefonu kraći tekst u poljima (npr. pretraga), desktop zadržava puni */
(function () {
  var els = document.querySelectorAll('[data-ph-short]');
  if (!els.length || !window.matchMedia) return;
  var mq = window.matchMedia('(max-width: 640px)');
  els.forEach(function (el) { el.dataset.phLong = el.getAttribute('placeholder') || ''; });
  function apply() { els.forEach(function (el) { el.setAttribute('placeholder', mq.matches ? el.dataset.phShort : el.dataset.phLong); }); }
  apply();
  mq.addEventListener ? mq.addEventListener('change', apply) : mq.addListener(apply);
})();

/* Veliki slider na naslovnoj: zavjesa-prelaz, naslov riječ po riječ, slova destinacije,
   traka napretka pokreće sljedeći slajd, strelice, prst, tastatura, blaga paralaksa mišem */
(function () {
  var hs = document.querySelector('.hs');
  if (!hs) return;
  var slides = hs.querySelectorAll('.hs-slide'), thumbs = hs.querySelectorAll('.hs-thumb'), n = slides.length, cur = 0, leaveT = 0, moveT = 0;
  var counter = hs.querySelector('.hs-cur');

  /* naslov u riječi, destinacija u slova (za animaciju) */
  hs.querySelectorAll('.hs-title a').forEach(function (a) {
    var words = a.textContent.trim().split(/\s+/);
    a.innerHTML = words.map(function (w, i) { return '<span class="w"><span style="--i:' + i + '">' + w.replace(/&/g, '&amp;').replace(/</g, '&lt;') + '</span></span>'; }).join(' ');
  });
  hs.querySelectorAll('.hs-dest').forEach(function (d) {
    var t = d.textContent.trim();
    d.innerHTML = Array.prototype.map.call(t, function (c, i) { return c === ' ' ? ' ' : '<span class="ch" style="--i:' + i + '">' + c.replace(/&/g, '&amp;').replace(/</g, '&lt;') + '</span>'; }).join('');
    d.style.setProperty('--len', Math.max(t.length, 4));
  });

  if (n < 2) { var nav = hs.querySelector('.hs-nav'); if (nav) nav.hidden = true; var cnt = hs.querySelector('.hs-count'); if (cnt) cnt.hidden = true; return; }

  /* sve slike unaprijed, da prelaz nikad ne pokaže praznu pozadinu */
  function preload() { slides.forEach(function (s) { var im = s.querySelector('img[loading="lazy"]'); if (im) im.removeAttribute('loading'); }); }
  if (document.readyState === 'complete') setTimeout(preload, 600); else window.addEventListener('load', function () { setTimeout(preload, 600); });

  function go(i, back) {
    i = (i + n) % n;
    if (i === cur) return;
    if (back === undefined) back = i < cur;
    hs.classList.toggle('is-back', back);
    slides.forEach(function (s) { s.classList.remove('is-leaving'); });
    void hs.offsetWidth; /* da novi slajd krene iz skrivene pozicije s prave strane */
    var prev = slides[cur], next = slides[i];
    prev.classList.remove('is-active'); prev.classList.add('is-leaving'); prev.setAttribute('aria-hidden', 'true');
    next.classList.add('is-active'); next.removeAttribute('aria-hidden');
    hs.classList.remove('is-moving'); void hs.offsetWidth; hs.classList.add('is-moving');
    clearTimeout(leaveT); leaveT = setTimeout(function () { prev.classList.remove('is-leaving'); }, 1400);
    clearTimeout(moveT); moveT = setTimeout(function () { hs.classList.remove('is-moving'); }, 1400);
    thumbs.forEach(function (t, k) {
      t.classList.toggle('is-active', k === i);
      t.setAttribute('aria-selected', k === i ? 'true' : 'false');
    });
    if (counter) {
      counter.textContent = (i + 1 < 10 ? '0' : '') + (i + 1);
      counter.classList.remove('is-flip'); void counter.offsetWidth; counter.classList.add('is-flip');
    }
    var im = next.querySelector('img[loading="lazy"]'); if (im) im.removeAttribute('loading');
    cur = i;
  }

  hs.addEventListener('animationend', function (e) { if (e.animationName === 'hs-prog') go(cur + 1, false); });
  thumbs.forEach(function (t) { t.addEventListener('click', function () { go(+t.dataset.go); }); });
  hs.querySelectorAll('.hs-arrow').forEach(function (b) { b.addEventListener('click', function () { go(cur + (+b.dataset.dir), +b.dataset.dir < 0); }); });
  hs.addEventListener('keydown', function (e) {
    if (e.key === 'ArrowRight') go(cur + 1, false); else if (e.key === 'ArrowLeft') go(cur - 1, true);
  });

  var mm = window.matchMedia ? window.matchMedia.bind(window) : function () { return { matches: false }; };
  if (mm('(hover: hover) and (pointer: fine)').matches) {
    hs.addEventListener('mouseenter', function () { hs.classList.add('is-paused'); });
    hs.addEventListener('mouseleave', function () { hs.classList.remove('is-paused'); hs.style.setProperty('--mx', 0); hs.style.setProperty('--my', 0); });
    if (!mm('(prefers-reduced-motion: reduce)').matches) {
      var raf = 0;
      hs.addEventListener('mousemove', function (e) {
        if (raf) return;
        raf = requestAnimationFrame(function () {
          raf = 0;
          var r = hs.getBoundingClientRect();
          hs.style.setProperty('--mx', ((e.clientX - r.left) / r.width - .5).toFixed(3));
          hs.style.setProperty('--my', ((e.clientY - r.top) / r.height - .5).toFixed(3));
        });
      });
    }
  }

  var x0 = null, y0 = 0;
  hs.addEventListener('touchstart', function (e) { x0 = e.touches[0].clientX; y0 = e.touches[0].clientY; }, { passive: true });
  hs.addEventListener('touchend', function (e) {
    if (x0 === null) return;
    var dx = e.changedTouches[0].clientX - x0, dy = e.changedTouches[0].clientY - y0;
    if (Math.abs(dx) > 50 && Math.abs(dx) > Math.abs(dy) * 1.3) go(cur + (dx < 0 ? 1 : -1), dx > 0);
    x0 = null;
  }, { passive: true });

  if (mm('(prefers-reduced-motion: reduce)').matches) hs.classList.add('is-paused');
})();

/* Brojke koje se odbroje od 0 do vrijednosti kad se pojave na ekranu */
(function () {
  var els = document.querySelectorAll('.js-count');
  if (!els.length) return;
  var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  function run(el) {
    if (el.dataset.done) return;
    el.dataset.done = '1';
    var to = parseInt(el.dataset.to, 10) || 0, t0 = null, dur = 1400 + Math.min(to, 40) * 15;
    if (reduce || to < 2) { el.textContent = to; return; }
    function step(t) {
      if (!t0) t0 = t;
      var p = Math.min((t - t0) / dur, 1), e = 1 - Math.pow(1 - p, 3);
      el.textContent = Math.round(to * e);
      if (p < 1) requestAnimationFrame(step);
    }
    el.textContent = '0';
    requestAnimationFrame(step);
    setTimeout(function () { el.textContent = to; }, dur + 400);
  }
  if (!('IntersectionObserver' in window)) { els.forEach(run); return; }
  var io = new IntersectionObserver(function (en) {
    en.forEach(function (x) { if (x.isIntersecting) { run(x.target); io.unobserve(x.target); } });
  }, { threshold: .6 });
  els.forEach(function (el) { io.observe(el); });
})();

/* Praznična tema (samo kad je body.is-festive): snježni rub s ledenicama i lampice koje se pale i trče,
   bokeh svjetla i snijeg u slojevima u zaglavlju stranice, zlatna čestitka i kapa na logu */
(function () {
  var body = document.body;
  if (!body.classList.contains('is-festive')) return;
  var NS = 'http://www.w3.org/2000/svg';
  var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var COLORS = ['#FF2E3F', '#FFC21A', '#22D36A', '#2FA8FF', '#FF5FD2', '#FF8A1F'];
  var YEAR = new Date().getMonth() === 0 ? new Date().getFullYear() : new Date().getFullYear() + 1;

  function el(n, a) { var e = document.createElementNS(NS, n); for (var k in a) e.setAttribute(k, a[k]); return e; }
  function rnd(a, b) { return a + Math.random() * (b - a); }

  // Snježni rub, ledenice i lampice (SVG preko cijele širine, ispod menija)
  var box = document.querySelector('.xl');
  function strand() {
    if (!box) return;
    var w = box.clientWidth, H = box.clientHeight, mob = w < 600, span = mob ? 70 : 96, n = Math.max(4, Math.round(w / span)), step = w / n, top = 11, sag = mob ? 14 : 20;
    var svg = el('svg', { viewBox: '0 0 ' + w + ' ' + H, preserveAspectRatio: 'none' }), defs = el('defs', {});
    COLORS.forEach(function (c, i) {
      var g = el('radialGradient', { id: 'xlg' + i });
      g.appendChild(el('stop', { offset: '0', 'stop-color': c, 'stop-opacity': '.95' }));
      g.appendChild(el('stop', { offset: '.35', 'stop-color': c, 'stop-opacity': '.45' }));
      g.appendChild(el('stop', { offset: '1', 'stop-color': c, 'stop-opacity': '0' }));
      defs.appendChild(g);
    });
    var ice = el('linearGradient', { id: 'xli', x1: 0, y1: 0, x2: 0, y2: 1 });
    ice.appendChild(el('stop', { offset: '0', 'stop-color': '#FFFFFF' }));
    ice.appendChild(el('stop', { offset: '1', 'stop-color': '#BFE3FF', 'stop-opacity': '.35' }));
    defs.appendChild(ice);
    var snow = el('linearGradient', { id: 'xls', x1: 0, y1: 0, x2: 0, y2: 1 });
    snow.appendChild(el('stop', { offset: '0', 'stop-color': '#FFFFFF' }));
    snow.appendChild(el('stop', { offset: '1', 'stop-color': '#DCEBFA' }));
    defs.appendChild(snow);
    svg.appendChild(defs);

    // žica u lukovima (iza snijega, da izgleda kao da izlazi ispod njega)
    var d = 'M0 ' + top, i;
    for (i = 0; i < n; i++) d += ' Q' + (i * step + step / 2) + ' ' + (top + sag * 2) + ' ' + ((i + 1) * step) + ' ' + top;
    svg.appendChild(el('path', { d: d, class: 'xl-wire' }));

    // ledenice
    var x = 4;
    while (x < w) {
      if (Math.random() < .55) {
        var L = rnd(5, mob ? 13 : 19), hw = rnd(1.6, 3.2);
        svg.appendChild(el('path', { d: 'M' + (x - hw) + ' 8 Q' + x + ' ' + (8 + L * .6) + ' ' + x + ' ' + (8 + L) + ' Q' + x + ' ' + (8 + L * .6) + ' ' + (x + hw) + ' 8 Z', fill: 'url(#xli)' }));
      }
      x += rnd(9, 20);
    }
    // snježni rub s grudvicama
    var cap = 'M0 -6 L' + w + ' -6 L' + w + ' 6', cx = w;
    while (cx > 0) { var nx = Math.max(0, cx - rnd(16, 38)); cap += ' Q' + ((cx + nx) / 2) + ' ' + rnd(10, 15) + ' ' + nx + ' ' + rnd(5, 8); cx = nx; }
    cap += ' Z';
    svg.appendChild(el('path', { d: cap, fill: 'rgba(10,30,80,.18)', transform: 'translate(0 2)' }));
    svg.appendChild(el('path', { d: cap, fill: 'url(#xls)' }));

    // sijalice (oblik C9), dvije po luku, nagnute niz žicu
    var k = 0, total = n * 2;
    for (i = 0; i < n; i++) {
      [0.3, 0.7].forEach(function (t) {
        var x0 = i * step, mx = x0 + step / 2, x1 = x0 + step, my = top + sag * 2;
        var px = (1 - t) * (1 - t) * x0 + 2 * (1 - t) * t * mx + t * t * x1, py = (1 - t) * (1 - t) * top + 2 * (1 - t) * t * my + t * t * top;
        var dx = 2 * (1 - t) * (mx - x0) + 2 * t * (x1 - mx), dy = 2 * (1 - t) * (my - top) + 2 * t * (top - my);
        var ang = Math.atan2(dy, dx) * 180 / Math.PI * .5, ci = k % COLORS.length, s = mob ? .85 : 1;
        var g = el('g', { transform: 'translate(' + px.toFixed(1) + ' ' + py.toFixed(1) + ') rotate(' + ang.toFixed(1) + ') scale(' + s + ')' });
        var delay = (-(k / total) * 2.8 * 3).toFixed(2) + 's';
        var glow = el('circle', { cx: 0, cy: 15, r: 30, fill: 'url(#xlg' + ci + ')', class: 'xl-g', style: 'animation-delay:' + delay });
        var bulb = el('path', { d: 'M-4.6 6 C-7.4 12 -5.6 21 0 25 C5.6 21 7.4 12 4.6 6 Z', fill: COLORS[ci], class: 'xl-b', style: 'animation-delay:' + delay });
        var shine = el('path', { d: 'M-2.6 9 C-3.8 13 -3 17 -1.2 19', fill: 'none', stroke: '#fff', 'stroke-width': 1.3, 'stroke-linecap': 'round', opacity: '.75' });
        var capEl = el('rect', { x: -3.6, y: 0, width: 7.2, height: 7, rx: 1.6, class: 'xl-cap' });
        g.appendChild(glow); g.appendChild(bulb); g.appendChild(shine); g.appendChild(capEl);
        svg.appendChild(g);
        k++;
      });
    }
    box.innerHTML = '';
    box.appendChild(svg);
  }
  strand();
  var rt; window.addEventListener('resize', function () { clearTimeout(rt); rt = setTimeout(strand, 200); });
  // lampice se pale nakon kratkog trenutka (efekat "uključivanja")
  if (!reduce) { body.classList.add('xl-boot'); setTimeout(function () { body.classList.remove('xl-boot'); }, 700); }

  // Djed Mraz pored loga: maše jednom rukom, a drugom pokazuje na logo
  var logo = document.querySelector('.site-head .logo');
  if (logo) logo.insertAdjacentHTML('beforeend', '<svg class="xl-santa" viewBox="0 0 64 84" aria-hidden="true"><path d="M15 84c0-17 7-26 17-26s17 9 17 26z" fill="#D7262E"/><path d="M32 58c-2 0-3 1-3 2v24h6V60c0-1-1-2-3-2z" fill="#fff"/><rect x="16" y="72" width="32" height="5" fill="#2B2B2B"/><rect x="28.5" y="71" width="7" height="7" rx="1" fill="none" stroke="#FFC23A" stroke-width="1.6"/><path d="M44 64l13-5" stroke="#D7262E" stroke-width="7" stroke-linecap="round"/><path d="M42 65l3-1" stroke="#fff" stroke-width="7"/><circle cx="58" cy="58.6" r="3.6" fill="#fff"/><path d="M60 57.5l3.4-1.2" stroke="#fff" stroke-width="2.4" stroke-linecap="round"/><g class="xl-wave"><path d="M20 63L10 49" stroke="#D7262E" stroke-width="7" stroke-linecap="round"/><circle cx="9" cy="46.5" r="4" fill="#fff"/></g><circle cx="32" cy="38" r="11" fill="#F7CBA8"/><path d="M20 38c-1 13 6 21 12 21s13-8 12-21c-3 6-21 6-24 0z" fill="#fff"/><ellipse cx="28" cy="44.6" rx="4.4" ry="2.4" fill="#fff"/><ellipse cx="36" cy="44.6" rx="4.4" ry="2.4" fill="#fff"/><circle cx="32" cy="41.4" r="2.2" fill="#EC9A84"/><circle cx="28" cy="37.2" r="1.3" fill="#1F2A33"/><circle cx="36" cy="37.2" r="1.3" fill="#1F2A33"/><circle cx="25.5" cy="41" r="1.8" fill="#F4A6A0" opacity=".7"/><circle cx="38.5" cy="41" r="1.8" fill="#F4A6A0" opacity=".7"/><path d="M26 34.2q2-1.4 4 0M34 34.2q2-1.4 4 0" stroke="#fff" stroke-width="1.4" fill="none" stroke-linecap="round"/><path d="M20 31c1-12 10-19 21-17 7 1 11 7 12 13-3-2-7-2-9 0 0 1 0 3 0 4z" fill="#D7262E"/><path d="M41 14c7 1 11 7 12 13-2-1-5-1-7 0-1-5-2-9-5-13z" fill="#AC1C24"/><rect x="18.5" y="29" width="27" height="6.4" rx="3.2" fill="#fff"/><circle cx="53.5" cy="28" r="4" fill="#fff"/></svg>');

  // Zaglavlje stranice: čestitka, bokeh svjetla i snijeg u tri sloja
  var hosts = document.querySelectorAll('.hs, .page-hero');
  Array.prototype.forEach.call(hosts, function (host) {
    if (getComputedStyle(host).position === 'static') host.style.position = 'relative';
    var greet = body.getAttribute('data-greet');
    if (greet) {
      host.insertAdjacentHTML('beforeend', '<div class="xl-greet" aria-hidden="true"><i></i><span></span><i></i></div>');
      host.querySelector('.xl-greet span').textContent = greet;
    }
    if (reduce) return;
    var cv = document.createElement('canvas'); cv.className = 'xs-snow'; cv.setAttribute('aria-hidden', 'true');
    host.appendChild(cv);
    var ctx = cv.getContext('2d'), W, H, dpr, flakes = [], bokeh = [], on = false, raf, t = 0;
    function flake(any) {
      var z = Math.random(), layer = z < .62 ? 0 : z < .93 ? 1 : 2;
      var r = layer === 0 ? rnd(.6, 1.5) : layer === 1 ? rnd(1.6, 2.8) : rnd(4.5, 8);
      return { x: Math.random() * W, y: any ? Math.random() * H : -12, r: r, l: layer, v: [.35, .8, 1.3][layer] + Math.random() * .3, s: Math.random() * 6.28, a: [.45, .75, .9][layer], rot: Math.random() * 6.28, vr: rnd(-.02, .02) };
    }
    function size() {
      dpr = Math.min(window.devicePixelRatio || 1, 2); W = host.clientWidth; H = host.clientHeight;
      cv.width = W * dpr; cv.height = H * dpr; ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
      var mob = W < 700, n = Math.round(Math.min(mob ? 70 : 170, W * H / (mob ? 6000 : 7000)));
      flakes = []; for (var i = 0; i < n; i++) flakes.push(flake(true));
      bokeh = []; var b = mob ? 7 : 14;
      for (var j = 0; j < b; j++) bokeh.push({ x: Math.random() * W, y: Math.random() * H, r: rnd(mob ? 24 : 36, mob ? 70 : 120), c: ['255,196,80', '255,70,80', '60,220,130', '80,170,255', '255,120,220'][j % 5], a: rnd(.08, .2), p: Math.random() * 6.28, vy: rnd(-.12, -.04), vx: rnd(-.06, .06) });
    }
    function crystal(f) {
      ctx.save(); ctx.translate(f.x, f.y); ctx.rotate(f.rot);
      ctx.strokeStyle = 'rgba(255,255,255,' + f.a + ')'; ctx.lineWidth = 1.2; ctx.lineCap = 'round';
      ctx.shadowColor = 'rgba(255,255,255,.8)'; ctx.shadowBlur = 6;
      ctx.beginPath();
      for (var i = 0; i < 6; i++) {
        var a = i * Math.PI / 3, cx = Math.cos(a), sy = Math.sin(a), r = f.r;
        ctx.moveTo(0, 0); ctx.lineTo(cx * r, sy * r);
        var bx = cx * r * .55, by = sy * r * .55, a1 = a + .6, a2 = a - .6, br = r * .32;
        ctx.moveTo(bx, by); ctx.lineTo(bx + Math.cos(a1) * br, by + Math.sin(a1) * br);
        ctx.moveTo(bx, by); ctx.lineTo(bx + Math.cos(a2) * br, by + Math.sin(a2) * br);
      }
      ctx.stroke(); ctx.restore();
    }
    function frame() {
      t += .016; ctx.clearRect(0, 0, W, H);
      ctx.globalCompositeOperation = 'lighter';
      for (var j = 0; j < bokeh.length; j++) {
        var b = bokeh[j]; b.x += b.vx; b.y += b.vy; b.p += .012;
        if (b.y < -b.r) { b.y = H + b.r; b.x = Math.random() * W; }
        var al = b.a * (.65 + .35 * Math.sin(b.p)), g = ctx.createRadialGradient(b.x, b.y, 0, b.x, b.y, b.r);
        g.addColorStop(0, 'rgba(' + b.c + ',' + al + ')'); g.addColorStop(.55, 'rgba(' + b.c + ',' + (al * .55) + ')'); g.addColorStop(1, 'rgba(' + b.c + ',0)');
        ctx.fillStyle = g; ctx.beginPath(); ctx.arc(b.x, b.y, b.r, 0, 6.283); ctx.fill();
      }
      ctx.globalCompositeOperation = 'source-over';
      var wind = Math.sin(t * .4) * .5;
      for (var i = 0; i < flakes.length; i++) {
        var f = flakes[i]; f.y += f.v; f.s += .015; f.x += Math.sin(f.s) * (.25 + f.l * .2) + wind * (f.l + 1) * .4; f.rot += f.vr;
        if (f.y > H + 12 || f.x < -20 || f.x > W + 20) flakes[i] = f = flake(false);
        if (f.l === 2) { crystal(f); continue; }
        ctx.beginPath(); ctx.fillStyle = 'rgba(255,255,255,' + f.a + ')'; ctx.arc(f.x, f.y, f.r, 0, 6.283); ctx.fill();
      }
      if (on) raf = requestAnimationFrame(frame);
    }
    size();
    var rs; window.addEventListener('resize', function () { clearTimeout(rs); rs = setTimeout(size, 250); });
    if ('IntersectionObserver' in window) {
      new IntersectionObserver(function (e) {
        host._xv = e[0].isIntersecting;
        if (host._xv && !on && !document.hidden) { on = true; raf = requestAnimationFrame(frame); } else if (!host._xv) { on = false; cancelAnimationFrame(raf); }
      }).observe(host);
    } else { host._xv = true; on = true; frame(); }
    document.addEventListener('visibilitychange', function () {
      if (document.hidden) { on = false; cancelAnimationFrame(raf); } else if (host._xv && !on) { on = true; raf = requestAnimationFrame(frame); }
    });
  });
})();

/* Praznična tema: veliki prozirni obrisi pahulja u pozadini sekcija (bijelih i plavih), ispod sadržaja */
(function () {
  if (!document.body.classList.contains('is-festive')) return;
  var arm = 'M50 50V6M50 18l-8-8M50 18l8-8M50 31l-12-10M50 31l12-10M50 41l-6-4M50 41l6-4';
  var flake = '<g fill="none" stroke-linecap="round" stroke-linejoin="round">';
  for (var i = 0; i < 6; i++) flake += '<path d="' + arm + '" transform="rotate(' + (i * 60) + ' 50 50)"/>';
  flake += '<path d="M50 40l8.7 5v10L50 60l-8.7-5V45z"/></g>';
  function lum(c) { var m = c.match(/\d+(\.\d+)?/g); if (!m) return 1; return (0.299 * m[0] + 0.587 * m[1] + 0.114 * m[2]) / 255; }
  var seed = 7; function rnd() { seed = (seed * 9301 + 49297) % 233280; return seed / 233280; }
  var secs = document.querySelectorAll('main > section, main > div > section, .site-foot, footer');
  Array.prototype.forEach.call(secs, function (sec) {
    if (sec.matches('.hs, .hero, .page-hero') || sec.offsetHeight < 160 || sec.querySelector(':scope > .xf-layer')) return;
    var cs = getComputedStyle(sec), darkBg = lum(cs.color) > .6;
    if (cs.position === 'static') sec.style.position = 'relative';
    sec.style.isolation = 'isolate';
    var layer = document.createElement('div'); layer.className = 'xf-layer'; layer.setAttribute('aria-hidden', 'true');
    var n = sec.offsetHeight > 700 ? 3 : 2, html = '';
    for (var k = 0; k < n; k++) {
      var size = Math.round(150 + rnd() * 170), left = k % 2 ? 72 + rnd() * 22 : -6 + rnd() * 18, top = (k + .3) / n * 100 - 10 + rnd() * 12;
      html += '<svg class="xf' + (darkBg ? ' on-dark' : '') + '" viewBox="0 0 100 100" style="width:' + size + 'px;height:' + size + 'px;left:' + left.toFixed(1) + '%;top:' + top.toFixed(1) + '%;animation-duration:' + Math.round(70 + rnd() * 60) + 's;animation-direction:' + (k % 2 ? 'reverse' : 'normal') + '">' + flake + '</svg>';
    }
    layer.innerHTML = html;
    sec.insertBefore(layer, sec.firstChild);
  });
})();
