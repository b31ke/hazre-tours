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
  const closeNav = () => { if (nav) { nav.classList.remove('open'); toggle.setAttribute('aria-expanded', 'false'); document.body.style.overflow = ''; } };
  toggle && toggle.addEventListener('click', () => {
    const open = nav.classList.toggle('open');
    toggle.setAttribute('aria-expanded', String(open));
    document.body.style.overflow = open ? 'hidden' : '';
  });
  nav && $$('a', nav).forEach(a => a.addEventListener('click', closeNav));

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
