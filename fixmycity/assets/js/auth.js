(() => {
  const tabs = [...document.querySelectorAll('[data-panel]')];
  const panels = [...document.querySelectorAll('.panel')];
  const titles = { login: ['Welcome back', 'Sign in to report an issue and track the things you care about.'], register: ['Join your community', 'Create your account to start making a difference.'], forgot: ['Reset your password', 'We’ll help you get back into your account.'] };
  function show(name) {
    if (!titles[name]) return;
    panels.forEach(panel => panel.hidden = panel.id !== name);
    tabs.forEach(tab => tab.setAttribute('aria-selected', String(tab.dataset.panel === name)));
    document.querySelector('#title').textContent = titles[name][0];
    document.querySelector('#subtitle').textContent = titles[name][1];
    history.replaceState(null, '', `#${name}`);
  }
  document.addEventListener('click', event => {
    const tab = event.target.closest('[data-panel]');
    const link = event.target.closest('[data-show]');
    if (tab) show(tab.dataset.panel);
    if (link) { event.preventDefault(); show(link.dataset.show); }
  });
  show(['login', 'register', 'forgot'].includes(location.hash.slice(1)) ? location.hash.slice(1) : 'login');
  fetch('fixmycity/index.php?action=csrf', { credentials: 'same-origin' })
    .then(response => response.json())
    .then(data => document.querySelectorAll('form.panel').forEach(form => {
      const token = document.createElement('input'); token.type = 'hidden'; token.name = 'csrf_token'; token.value = data.token; form.append(token);
    }))
    .catch(() => {});
  const params = new URLSearchParams(location.search);
  const message = params.get('message');
  if (message) {
    const notice = document.querySelector('#notice');
    notice.textContent = ({ registered: 'Account created. You can now sign in.', invalid: 'Please check your details and try again.', credentials: 'Email or password was not recognized.', exists: 'That username or email is already registered.', sent: 'If that email is registered, a reset link has been sent.', reset: 'Your password was updated. Sign in with your new password.', expired: 'That reset link is invalid or has expired.' })[message] || 'Please try again.';
    notice.hidden = false;
    if (['registered', 'reset'].includes(message)) show('login');
    if (message === 'expired') show('forgot');
  }
  const resetToken = params.get('reset');
  if (resetToken) {
    show('forgot');
    const form = document.querySelector('#forgot');
    form.action = 'fixmycity/index.php?action=reset_password';
    form.querySelector('.sub').textContent = 'Choose a new password for your account.';
    form.querySelector('label').textContent = 'New password';
    const email = form.querySelector('[name=email]');
    email.type = 'password'; email.name = 'password'; email.id = 'reset-password'; email.minLength = 8; email.required = true;
    const token = document.createElement('input'); token.type = 'hidden'; token.name = 'token'; token.value = resetToken; form.append(token);
    form.querySelector('button').textContent = 'Update password';
  }
})();
