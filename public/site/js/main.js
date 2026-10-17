const burger = document.getElementById('burger');
const menu = document.getElementById('menu');
if (burger && menu) {
  burger.addEventListener('click', () => {
    const open = menu.classList.toggle('hidden') === false;
    burger.setAttribute('aria-expanded', open);
  });
}
const io = new IntersectionObserver(entries => {
  entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('show'); io.unobserve(e.target); } });
}, { threshold: 0.12 });
document.querySelectorAll('.reveal').forEach(el => io.observe(el));
document.querySelectorAll('[data-year]').forEach(el => el.textContent = new Date().getFullYear());

// Gallery filter
const filterBtns = document.querySelectorAll('[data-filter]');
const galleryItems = document.querySelectorAll('[data-cat]');
filterBtns.forEach(btn => {
  btn.addEventListener('click', () => {
    const f = btn.dataset.filter;
    filterBtns.forEach(b => {
      b.classList.remove('bg-pk-red', 'text-white');
      b.classList.add('bg-white', 'text-pk-dark');
    });
    btn.classList.remove('bg-white', 'text-pk-dark');
    btn.classList.add('bg-pk-red', 'text-white');
    galleryItems.forEach(item => {
      item.classList.toggle('hidden', f !== 'all' && item.dataset.cat !== f);
    });
  });
});

// Contact form -> opens WhatsApp with the filled details (no backend needed)
const WA_NUMBER = '8801XXXXXXXXX'; // <-- put your WhatsApp number here (country code, no + or spaces)
const contactForm = document.getElementById('contactForm');
if (contactForm) {
  contactForm.addEventListener('submit', e => {
    e.preventDefault();
    const d = new FormData(contactForm);
    const text =
      'Hello Probashi Kormo,%0A' +
      'Name: ' + encodeURIComponent(d.get('name')) + '%0A' +
      'Phone: ' + encodeURIComponent(d.get('phone')) + '%0A' +
      'Country: ' + encodeURIComponent(d.get('country')) + '%0A' +
      'Trade/Job: ' + encodeURIComponent(d.get('trade') || '-') + '%0A' +
      'Message: ' + encodeURIComponent(d.get('message') || '-');
    window.open('https://wa.me/' + WA_NUMBER + '?text=' + text, '_blank', 'noopener');
  });
}

// Apply form -> WhatsApp (same WA_NUMBER as above)
const applyForm = document.getElementById('applyForm');
if (applyForm) {
  const job = new URLSearchParams(location.search).get('job');
  if (job && applyForm.elements['job']) applyForm.elements['job'].value = job;
  applyForm.addEventListener('submit', e => {
    e.preventDefault();
    const d = new FormData(applyForm);
    const v = k => encodeURIComponent(d.get(k) || '-');
    const text =
      'New job application%0A' +
      'Name: ' + v('name') + '%0A' +
      'Phone: ' + v('phone') + '%0A' +
      'Age: ' + v('age') + '%0A' +
      'District: ' + v('district') + '%0A' +
      'Passport: ' + v('passport') + '%0A' +
      'Education: ' + v('education') + '%0A' +
      'Country: ' + v('country') + '%0A' +
      'Job/Trade: ' + v('job') + '%0A' +
      'Experience: ' + v('experience') + '%0A' +
      'Message: ' + v('message');
    window.open('https://wa.me/' + WA_NUMBER + '?text=' + text, '_blank', 'noopener');
  });
}

// Gallery lightbox (works when a figure contains a real <img>)
const lb = document.getElementById('lightbox');
if (lb) {
  const lbImg = document.getElementById('lbImg');
  const lbCap = document.getElementById('lbCap');
  const closeLb = () => { lb.classList.add('hidden'); lb.classList.remove('flex'); };
  document.querySelectorAll('figure[data-cat] img').forEach(img => {
    img.classList.add('cursor-zoom-in');
    img.addEventListener('click', () => {
      lbImg.src = img.src;
      lbCap.textContent = img.alt;
      lb.classList.remove('hidden');
      lb.classList.add('flex');
    });
  });
  document.getElementById('lbClose').addEventListener('click', closeLb);
  lb.addEventListener('click', e => { if (e.target === lb) closeLb(); });
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLb(); });
}
