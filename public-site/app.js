document.getElementById('year').textContent = new Date().getFullYear();

const sections = document.querySelectorAll('.reveal');

if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add('visible');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.12 });

  sections.forEach((section) => observer.observe(section));
} else {
  sections.forEach((section) => section.classList.add('visible'));
}

const explorerPanel = document.getElementById('explorer-panel');
const explorerTemplate = document.getElementById('explorer-template');
explorerPanel.addEventListener('toggle', () => {
  if (explorerPanel.open) {
    if (!explorerPanel.querySelector('iframe')) explorerPanel.append(explorerTemplate.content.cloneNode(true));
  } else {
    explorerPanel.querySelector('iframe')?.remove();
  }
});
explorerPanel.hidden = false;
