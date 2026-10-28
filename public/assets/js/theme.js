(function () {
  const storageKey = 'sms-theme';
  const root = document.documentElement;
  const storedTheme = localStorage.getItem(storageKey);
  let theme = storedTheme === 'dark' ? 'dark' : 'light';

  const applyTheme = () => {
    root.dataset.theme = theme;
    const button = document.querySelector('[data-theme-toggle]');
    if (!button) return;
    button.textContent = theme === 'dark' ? 'Light mode' : 'Dark mode';
    button.setAttribute('aria-label', `Switch to ${theme === 'dark' ? 'light' : 'dark'} theme`);
    button.setAttribute('aria-pressed', String(theme === 'dark'));
  };

  const header = document.querySelector('header');
  const headerRow = header && header.querySelector('.flex.items-center.justify-between');
  if (headerRow) {
    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'theme-toggle';
    button.dataset.themeToggle = '';
    button.addEventListener('click', () => {
      theme = theme === 'dark' ? 'light' : 'dark';
      localStorage.setItem(storageKey, theme);
      applyTheme();
    });
    headerRow.appendChild(button);
  }

  applyTheme();
})();
