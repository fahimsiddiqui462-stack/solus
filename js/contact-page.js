(() => {
 const form = document.getElementById('contact-form');
 if (!form) return;
 form.addEventListener('submit', async event => {
  event.preventDefault();
  const button = form.querySelector('button[type="submit"]'), status = document.getElementById('status');
  if (button.disabled || !form.reportValidity()) return;
  button.disabled = true; status.textContent = 'Sending…';
  try {
   const tokenResponse = await fetch('api/csrf.php', {credentials:'same-origin'});
   if (!tokenResponse.ok) throw new Error('Backend unavailable');
   const token = await tokenResponse.json();
   if (!token.csrf) throw new Error('Missing session token');
   const body = new FormData(form); body.set('csrf', token.csrf);
   const response = await fetch('api/contact.php', {method:'POST', body, credentials:'same-origin'});
   const result = await response.json();
   status.textContent = result.message || 'Your message was not sent. Please try again.';
   if (response.ok && result.ok) form.reset();
  } catch {
   status.textContent = 'Your message was not sent. A configured PHP/MySQL server is required. Please try again when the service is available.';
  } finally { button.disabled = false; }
 });
})();
