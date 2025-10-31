async function sha256(str) {
  const buf = await crypto.subtle.digest('SHA-256', new TextEncoder().encode(str));
  return Array.from(new Uint8Array(buf)).map(b => b.toString(16).padStart(2,'0')).join('');
}

const DEMO_ADMIN = { username: 'admin', pwHash: null };

(async ()=>{
  DEMO_ADMIN.pwHash = await sha256('Admin@123'); // demo password, computed
  // initialize storage if empty
  if(!localStorage.getItem('flashhotel_bookings')) {
    const sample = [
      {id:1,name:'Alice Tan',room:'Deluxe',checkin:'2025-11-12',checkout:'2025-11-15',price:300},
      {id:2,name:'Mohamed Ali',room:'Standard',checkin:'2025-12-01',checkout:'2025-12-02',price:120}
    ];
    localStorage.setItem('flashhotel_bookings', JSON.stringify(sample));
  }
})();

// --- Auth ---
async function login(e){
  e && e.preventDefault();
  const u = document.getElementById('username').value.trim();
  const p = document.getElementById('password').value;
  const errEl = document.getElementById('login-error');
  errEl.textContent = '';
  const hashed = await sha256(p);
  if(u === DEMO_ADMIN.username && hashed === DEMO_ADMIN.pwHash) {
    localStorage.setItem('flashhotel_admin', JSON.stringify({username:u,at:Date.now()}));
    showAdminScreen(u);
    return false;
  } else {
    errEl.textContent = 'Invalid username or password (demo).';
    return false;
  }
}

function logout(){
  localStorage.removeItem('flashhotel_admin');
  document.getElementById('admin-screen').style.display = 'none';
  document.getElementById('login-screen').style.display = 'block';
}

// --- Page state ---
function showAdminScreen(username){
  document.getElementById('login-screen').style.display = 'none';
  document.getElementById('admin-screen').style.display = 'block';
  document.getElementById('admin-logged-as').textContent = `Signed in as ${username}`;
  renderBookings();
}

window.onload = function(){
  const admin = JSON.parse(localStorage.getItem('flashhotel_admin') || 'null');
  if(admin) showAdminScreen(admin.username);
}

// --- Bookings CRUD using localStorage ---
function getBookings(){
  const raw = localStorage.getItem('flashhotel_bookings') || '[]';
  return JSON.parse(raw);
}
function saveBookings(arr){ localStorage.setItem('flashhotel_bookings', JSON.stringify(arr)); }

function renderBookings(){
  const tbody = document.querySelector('#bookings-table tbody');
  tbody.innerHTML = '';
  const all = getBookings();
  const q = (document.getElementById('search').value || '').toLowerCase();
  const filtered = all.filter(b => !q || JSON.stringify(b).toLowerCase().includes(q));
  filtered.forEach(b=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${b.id}</td>
                    <td>${escapeHtml(b.name)}</td>
                    <td>${escapeHtml(b.room)}</td>
                    <td>${b.checkin}</td>
                    <td>${b.checkout}</td>
                    <td>${b.price}</td>
                    <td>
                      <button class="action-btn" onclick="editBooking(${b.id})">Edit</button>
                      <button class="action-btn danger" onclick="deleteBooking(${b.id})">Delete</button>
                    </td>`;
    tbody.appendChild(tr);
  });
}

// Simple HTML escape
function escapeHtml(s){ return (s||'').replaceAll('&','&amp;').replaceAll('<','&lt;').replaceAll('>','&gt;'); }

function showNewBookingForm(){
  document.getElementById('booking-form-section').style.display='block';
  document.getElementById('bookings-section').style.display='none';
  document.getElementById('form-title').textContent='New Booking';
  document.getElementById('bookingForm').reset();
  document.getElementById('booking-id').value = '';
}

function hideBookingForm(){
  document.getElementById('booking-form-section').style.display='none';
  document.getElementById('bookings-section').style.display='block';
}

function saveBooking(e){
  e && e.preventDefault();
  const id = document.getElementById('booking-id').value;
  const name = document.getElementById('guest-name').value.trim();
  const room = document.getElementById('room-type').value.trim();
  const checkin = document.getElementById('check-in').value;
  const checkout = document.getElementById('check-out').value;
  const price = Number(document.getElementById('price').value);
  // basic validation
  if(new Date(checkout) < new Date(checkin)){
    alert('Check-out must be after check-in.');
    return false;
  }
  const arr = getBookings();
  if(id){
    const idx = arr.findIndex(x=>x.id==id);
    if(idx>=0) arr[idx] = {id:Number(id),name,room,checkin,checkout,price};
  } else {
    const newId = arr.reduce((m,x)=>Math.max(m,x.id||0),0)+1;
    arr.push({id:newId,name,room,checkin,checkout,price});
  }
  saveBookings(arr);
  hideBookingForm();
  renderBookings();
  return false;
}

function editBooking(id){
  const arr = getBookings();
  const b = arr.find(x=>x.id==id);
  if(!b) return alert('Not found');
  document.getElementById('booking-id').value = b.id;
  document.getElementById('guest-name').value = b.name;
  document.getElementById('room-type').value = b.room;
  document.getElementById('check-in').value = b.checkin;
  document.getElementById('check-out').value = b.checkout;
  document.getElementById('price').value = b.price;
  document.getElementById('form-title').textContent='Edit Booking';
  document.getElementById('booking-form-section').style.display='block';
  document.getElementById('bookings-section').style.display='none';
}

function deleteBooking(id){
  if(!confirm('Delete booking #' + id + '?')) return;
  const arr = getBookings().filter(x=>x.id!=id);
  saveBookings(arr);
  renderBookings();
}

function exportCSV(){
  const arr = getBookings();
  if(arr.length===0) return alert('No bookings to export');
  const header = ['id','name','room','checkin','checkout','price'];
  const csv = [header.join(',')].concat(arr.map(r=>header.map(h => `"${(r[h]||'').toString().replace(/"/g,'""')}"`).join(','))).join('\r\n');
  const blob = new Blob([csv], {type:'text/csv'});
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = 'bookings.csv';
  a.click();
  URL.revokeObjectURL(url);
}
