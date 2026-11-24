document.addEventListener('submit', function (event) {
  const form = event.target;

  if (!form.hasAttribute('data-validate')) {
    return;
  }

  const fields = form.querySelectorAll('[data-required]');
  let message = '';

  fields.forEach(function (field) {
    if (field.value.trim() === '') {
      const label = form.querySelector('label[for="' + field.id + '"]');
      const name = label ? label.textContent : field.name;
      message += name + ' is required.\n';
    }
  });

  if (message !== '') {
    event.preventDefault();
    alert(message);
  }
});
