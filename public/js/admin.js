// Hazre Tours admin: označi aktivnu stavku u podstablu "Aranžmani i upiti" prema adresi (radi i uz pjax navigaciju admina)
(() => {
  let last = '';
  const mark = () => {
    const path = location.pathname;
    if (path === last) return;
    last = path;
    const links = document.querySelectorAll('.hz-sub__link');
    if (!links.length) return;
    let hit = null;
    links.forEach(a => { if (path.includes('/' + a.dataset.slug)) hit = a; });
    // na samom /admin/aranzmani je prva kartica (Aranžmani)
    if (!hit && /\/admin\/aranzmani\/?$/.test(path)) hit = links[0];
    links.forEach(a => a.classList.toggle('is-current', a === hit));
  };
  setInterval(mark, 400);
  document.addEventListener('DOMContentLoaded', mark);
})();
