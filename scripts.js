// scripts.js (cleaned) - Flash Hotel Admin
// Assumes API endpoints live at http://localhost/FLASH-HOTEL-MANAGEMENT-SYSTEM/api

const API_BASE = "http://localhost/FLASH-HOTEL-MANAGEMENT-SYSTEM/api";

// -----------------------------
// Utility: SHA-256 (for optional local hashing)
// -----------------------------
async function sha256(str) {
  const buf = await crypto.subtle.digest('SHA-256', new TextEncoder().encode(str));
  return Array.from(new Uint8Array(buf)).map(b => b.toString(16).padStart(2,'0')).join('');
}

// -----------------------------
// Auth: login / logout / session
// -----------------------------
async function login(e){
  e && e.preventDefault();
  const u = document.getElementById('username').value.trim();
  const p = document.getElementById('password').value;
  const errEl = document.getElementById('login-error');
  errEl.textContent = '';

  if(!u || !p){
    errEl.textContent = 'Please enter username and password.';
    return false;
  }

  // Use FormData so PHP can read it easily
  const fd = new FormData();
  fd.append('username', u);
  fd.append('password', p);

  try {
    const res = await fetch(`${API_BASE}/api/admin.php`, { method: 'POST', body: fd });
    const text = await res.text();
    if(text.trim() === 'success'){
      // keep a simple client-side flag so UI persists across reloads
      localStorage.setItem('flashhotel_admin', JSON.stringify({ username: u, at: Date.now() }));
      showAdminScreen(u);
    } else {
      errEl.textContent = 'Invalid username or password.';
    }
  } catch (err) {
    console.error('Login error', err);
    errEl.textContent = 'Error contacting server.';
  }

  return false;
}

function logout(){
  localStorage.removeItem('flashhotel_admin');
  document.getElementById('admin-screen').style.display = 'none';
  document.getElementById('login-screen').style.display = 'block';
}

// -----------------------------
// Page state / show screens
// -----------------------------
function showAdminScreen(username){
  document.getElementById('login-screen').style.display = 'none';
  document.getElementById('admin-screen').style.display = 'block';
  document.getElementById('admin-logged-as').textContent = `Signed in as ${username}`;
  renderBookings();
}

window.addEventListener('DOMContentLoaded', ()=>{
  const admin = JSON.parse(localStorage.getItem('flashhotel_admin') || 'null');
  if(admin && admin.username) showAdminScreen(admin.username);
});

// -----------------------------
// Basic helpers
// -----------------------------
function escapeHtml(s){ return (s||'').replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;'); }

function showNewBookingForm(){
  document.getElementById('booking-form-section').style.display='block';
  const bookingsSection = document.querySelector('.bookings');
  if(bookingsSection) bookingsSection.style.display = 'none';
  document.getElementById('form-title').textContent='New Booking';
  document.getElementById('bookingForm').reset();
  document.getElementById('booking-id').value = '';
}

function hideBookingForm(){
  document.getElementById('booking-form-section').style.display='none';
  const bookingsSection = document.querySelector('.bookings');
  if(bookingsSection) bookingsSection.style.display = 'block';
}

// -----------------------------
// API calls
// -----------------------------
async function apiFetchJSON(url, opts){
  try {
    const res = await fetch(url, opts);
    if(!res.ok) throw new Error('HTTP ' + res.status);
    return await res.json();
  } catch (err){
    console.error('apiFetchJSON', url, err);
    throw err;
  }
}

async function getBookings(){
  try {
    return await apiFetchJSON(`${API_BASE}/get_bookings.php`);
  } catch (err) {
    // return empty array when fail so UI doesn't break
    return [];
  }
}

// -----------------------------
// Render bookings table
// -----------------------------
async function renderBookings(){
  const tbody = document.querySelector('#bookings-table tbody');
  if(!tbody) return;
  tbody.innerHTML = '';

  const all = await getBookings();
  if(!Array.isArray(all) || all.length === 0){
    tbody.innerHTML = '<tr><td colspan="7">No bookings found.</td></tr>';
    return;
  }

  all.forEach(b => {
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${escapeHtml(String(b.id))}</td>
      <td>${escapeHtml(b.guest_name)}</td>
      <td>${escapeHtml(b.room_type)}</td>
      <td>${escapeHtml(b.check_in)}</td>
      <td>${escapeHtml(b.check_out)}</td>
      <td>${escapeHtml(String(b.price))}</td>
      <td>
        <button class="action-btn" onclick="editBooking(${b.id})">Edit</button>
        <button class="action-btn danger" onclick="deleteBooking(${b.id})">Delete</button>
      </td>
    `;
    tbody.appendChild(tr);
  });
}

// -----------------------------
// Edit / Delete / Save (add/update)
// -----------------------------
async function editBooking(id){
  const all = await getBookings();
  const b = all.find(x => Number(x.id) === Number(id));
  if(!b) return alert('Booking not found');

  document.getElementById('booking-id').value = b.id;
  document.getElementById('guest-name').value = b.guest_name;
  document.getElementById('room-type').value = b.room_type;
  document.getElementById('check-in').value = b.check_in;
  document.getElementById('check-out').value = b.check_out;
  document.getElementById('price').value = b.price;

  document.getElementById('form-title').textContent = 'Edit Booking';
  document.getElementById('booking-form-section').style.display = 'block';
  const bookingsSection = document.querySelector('.bookings');
  if(bookingsSection) bookingsSection.style.display = 'none';
}

async function deleteBooking(id){
  if(!confirm('Delete booking #' + id + '?')) return;
  try {
    // Accepting GET query param delete for simplicity; if your PHP expects POST, change accordingly
    const res = await fetch(`${API_BASE}/delete_booking.php?id=${encodeURIComponent(id)}`);
    // optionally check response
    if(res.ok) renderBookings();
    else throw new Error('Delete failed');
  } catch (err){
    console.error('deleteBooking', err);
    alert('Error deleting booking');
  }
}

async function saveBooking(e){
  e && e.preventDefault();

  const id = document.getElementById('booking-id').value || null;
  const guest_name = document.getElementById('guest-name').value.trim();
  const room_type = document.getElementById('room-type').value.trim();
  const check_in = document.getElementById('check-in').value;
  const check_out = document.getElementById('check-out').value;
  const price = document.getElementById('price').value;

  // basic validation
  if(!guest_name || !room_type || !check_in || !check_out || price === ''){
    return alert('Please fill all required fields');
  }
  if(new Date(check_out) < new Date(check_in)){
    return alert('Check-out must be after check-in');
  }

  const payload = { id: id ? Number(id) : null, guest_name, room_type, check_in, check_out, price };
  const endpoint = id ? 'update_booking.php' : 'add_booking.php';

  try {
    const resp = await fetch(`${API_BASE}/${endpoint}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });

    const json = await resp.json();
    if(resp.ok && (json.message || json.success)){
      hideBookingForm();
      renderBookings();
      alert(id ? 'Booking updated' : 'Booking added');
    } else {
      console.error('saveBooking error', json);
      alert('Error saving booking: ' + (json.error || JSON.stringify(json)));
    }
  } catch (err){
    console.error('saveBooking', err);
    alert('Error saving booking');
  }
}

// -----------------------------
// Export CSV
// -----------------------------
async function exportCSV(){
  const arr = await getBookings();
  if(!Array.isArray(arr) || arr.length === 0) return alert('No bookings to export');

  const header = ['id','guest_name','room_type','check_in','check_out','price'];
  const csvRows = [header.join(',')];
  arr.forEach(r => {
    const row = header.map(h => {
      const cell = (r[h] === null || r[h] === undefined) ? '' : String(r[h]);
      return '"' + cell.replace(/"/g,'""') + '"';
    }).join(',');
    csvRows.push(row);
  });

  const blob = new Blob([csvRows.join('\r\n')], { type: 'text/csv' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = 'bookings.csv';
  document.body.appendChild(a);
  a.click();
  a.remove();
  URL.revokeObjectURL(url);
}

// -----------------------------
// Attach form handlers (if not wired inline in HTML)
// -----------------------------
(function attachHandlers(){
  // Booking form may already have onsubmit in HTML — attaching defensively
  const bookingForm = document.getElementById('bookingForm');
  if(bookingForm){
    bookingForm.removeEventListener('submit', saveBooking);
    bookingForm.addEventListener('submit', saveBooking);
  }

  // If login form is inline on HTML (onsubmit="return login(event)"), this is redundant but harmless
  const loginForm = document.getElementById('loginForm');
  if(loginForm){
    loginForm.removeEventListener('submit', login);
    loginForm.addEventListener('submit', login);
  }
})();

 function login(event) {
    event.preventDefault();

    let username = document.getElementById("username").value;
    let password = document.getElementById("password").value;

    let formData = new FormData();
    formData.append("username", username);
    formData.append("password", password);

   fetch("api/admin.php", {
    method: "POST",
    body: formData
   })


    .then(res => res.text())
    .then(data => {
        if (data.trim() === "success") {
            document.getElementById("login-screen").style.display = "none";
            document.getElementById("admin-screen").style.display = "block";
            document.getElementById("admin-logged-as").innerText = "Logged in as: " + username;
        } else {
            document.getElementById("login-error").innerText = "Invalid username or password";
        }
    });

    return false;
}
function logout() {
    document.getElementById("admin-screen").style.display = "none";
    document.getElementById("login-screen").style.display = "block";
}

function renderBookings() {
    fetch("get_bookings.php")
    .then(res => res.json())
    .then(data => {
        let tbody = document.querySelector("#bookings-table tbody");
        let search = document.getElementById("search").value.toLowerCase();
        tbody.innerHTML = "";

        data.filter(b => 
            b.guest_name.toLowerCase().includes(search)
        ).forEach(b => {
            tbody.innerHTML += `
                <tr>
                    <td>${b.id}</td>
                    <td>${b.guest_name}</td>
                    <td>${b.room_type}</td>
                    <td>${b.check_in}</td>
                    <td>${b.check_out}</td>
                    <td>${b.price}</td>
                    <td>
                        <button onclick="editBooking(${b.id}, '${b.guest_name}', '${b.room_type}', '${b.check_in}', '${b.check_out}', '${b.price}')">Edit</button>
                        <button onclick="deleteBooking(${b.id})">Delete</button>
                    </td>
                </tr>
            `;
        });
    });
}

function saveBooking(event) {
    event.preventDefault();

    let formData = new FormData();
    formData.append("id", document.getElementById("booking-id").value);
    formData.append("guest_name", document.getElementById("guest-name").value);
    formData.append("room_type", document.getElementById("room-type").value);
    formData.append("check_in", document.getElementById("check-in").value);
    formData.append("check_out", document.getElementById("check-out").value);
    formData.append("price", document.getElementById("price").value);

    fetch("save_booking.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(() => {
        hideBookingForm();
        renderBookings();
    });
}

function deleteBooking(id) {
    let formData = new FormData();
    formData.append("id", id);

    fetch("delete_booking.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(() => renderBookings());
}
