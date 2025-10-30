// static/script.js - small client validation
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('loginForm');
  if (!form) return;
  form.addEventListener('submit', function (e) {
    const u = document.getElementById('username').value.trim();
    const p = document.getElementById('password').value;
    if (!u || !p) {
      e.preventDefault();
      alert('Please provide both username and password.');
      return false;
    }
    // Optional: add client-side password strength check here
  });
});
