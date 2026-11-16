// Hazre Tours: meni na telefonu, pretraga i filter aranžmana na početnoj, forma za upit.
(function () {
  'use strict';
  const $ = (s, el = document) => el.querySelector(s);
  const $$ = (s, el = document) => [...el.querySelectorAll(s)];

  // Mobilni meni
  const menu = $('#menu');
  const menuBtn = $('#menu-btn');
  const closeMenu = () => { if (menu) { menu.classList.remove('open'); menuBtn.setAttribute('aria-expanded', 'false'); } };
  if (menu && menuBtn) {
    menuBtn.addEventListener('click', () => menuBtn.setAttribute('aria-expanded', String(menu.classList.toggle('open'))));
    $$('a', menu).forEach(a => a.addEventListener('click', closeMenu));
  }

  // Filter i pretraga aranžmana (samo na početnoj)
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
          && (!q || c.dataset.search.includes(q))
          && (!st.month || c.dataset.months.split('|').includes(st.month))
          && (!st.from || c.dataset.from.split('|').includes(st.from));
        c.hidden = !ok;
        if (ok) shown++;
      });
      empty.hidden = shown > 0;
      const filtered = q || st.month || st.from;
      note.hidden = !filtered;
      if (filtered) {
        note.innerHTML = 'Pronađeno: <b>' + shown + '</b> ' + (shown === 1 ? 'aranžman' : 'aranžmana') + '. <button type="button" id="clear">Poništi pretragu</button>';
      }
    };
    const setType = type => {
      st.type = type;
      $$('.chip').forEach(c => c.setAttribute('aria-pressed', String(c.dataset.type === type)));
      const sel = $('#s-type');
      if (sel) sel.value = type;
      apply();
    };

    $$('.chip').forEach(c => c.addEventListener('click', () => setType(c.dataset.type)));
    $$('[data-filter]').forEach(a => a.addEventListener('click', e => {
      e.preventDefault();
      setType(a.dataset.filter);
      $('#aranzmani').scrollIntoView();
      closeMenu();
    }));
    const search = $('#search');
    search && search.addEventListener('submit', e => {
      e.preventDefault();
      st.q = $('#s-q').value;
      st.month = $('#s-month').value;
      st.from = $('#s-from').value;
      setType($('#s-type').value);
      $('#aranzmani').scrollIntoView();
    });
    note.addEventListener('click', e => {
      if (e.target.id !== 'clear') return;
      Object.assign(st, { q: '', month: '', from: '' });
      search.reset();
      setType('sve');
    });
    // Link iz menija s druge stranice: /?vrsta=jednodnevni#aranzmani
    const vrsta = new URLSearchParams(location.search).get('vrsta');
    if (vrsta) setType(vrsta);
  }

  // Forma za upit: termini izabranog aranžmana, slanje bez osvježavanja stranice
  const form = $('#inq');
  if (form) {
    let terms = {};
    try { terms = JSON.parse($('#form-terms').textContent || '{}'); } catch (e) { terms = {}; }
    const tripSel = $('#f-trip');
    const termSel = $('#f-term');
    const fillTerms = () => {
      const list = terms[tripSel.value] || [];
      termSel.innerHTML = list.length
        ? list.map(t => '<option>' + t.replace(/</g, '&lt;') + '</option>').join('')
        : '<option value="">' + (tripSel.value ? 'Nema slobodnih termina' : 'Izaberite aranžman') + '</option>';
    };
    tripSel.addEventListener('change', fillTerms);
    fillTerms();

    $$('[data-msg]').forEach(a => a.addEventListener('click', () => { $('#f-msg').value = a.dataset.msg; }));

    const msg = $('#inq-msg');
    form.addEventListener('submit', async e => {
      e.preventDefault();
      $$('.err', form).forEach(el => { el.hidden = true; el.textContent = ''; });
      $$('.field', form).forEach(f => f.classList.remove('has-err'));
      const btn = $('button[type=submit]', form);
      btn.disabled = true;
      try {
        const res = await fetch(form.action, {
          method: 'POST',
          body: new FormData(form),
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
        });
        const data = await res.json().catch(() => ({}));
        msg.hidden = false;
        msg.classList.toggle('bad', !data.ok);
        if (data.ok) {
          msg.textContent = data.message;
          form.reset();
          fillTerms();
        } else {
          msg.textContent = data.error || (res.status === 429 ? 'Previše pokušaja. Pokušajte ponovo za minut.' : 'Upit nije poslan. Pozovite nas telefonom.');
          Object.entries(data.fields || {}).forEach(([name, text]) => {
            const el = $('[data-err="' + name + '"]', form);
            if (el) { el.textContent = text; el.hidden = false; el.closest('.field')?.classList.add('has-err'); }
          });
        }
      } catch (err) {
        msg.hidden = false;
        msg.classList.add('bad');
        msg.textContent = 'Nema veze s internetom. Pokušajte ponovo ili nas pozovite.';
      } finally {
        btn.disabled = false;
      }
    });
  }
})();
