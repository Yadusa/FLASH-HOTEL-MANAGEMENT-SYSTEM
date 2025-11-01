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

const API_BASE = "http://localhost/FLASH-HOTEL-MANAGEMENT-SYSTEM/api";

async function getBookings() {
  const res = await fetch(`${API_BASE}/get_bookings.php`);
  return await res.json();
}

async function saveBooking(e) {
  e.preventDefault();
  const id = document.getElementById('booking-id').value;
  const data = {
    id: id ? Number(id) : null,
    guest_name: document.getElementById('guest-name').value,
    room_type: document.getElementById('room-type').value,
    check_in: document.getElementById('check-in').value,
    check_out: document.getElementById('check-out').value,
    price: Number(document.getElementById('price').value)
  };

  const endpoint = id ? "update_booking.php" : "add_booking.php";

  const res = await fetch(`${API_BASE}/${endpoint}`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data)
  });

  const result = await res.json();
  if (result.message) {
    alert(id ? "Booking updated!" : "Booking saved!");
    hideBookingForm();
    renderBookings();
  } else {
    alert("Error: " + result.error);
  }
}

async function renderBookings(){
  const tbody = document.querySelector('#bookings-table tbody');
  tbody.innerHTML = '';
  const all = await getBookings();
  console.log("Fetched data:", all);

  if (!Array.isArray(all)) {
    tbody.innerHTML = `<tr><td colspan="7" style="color:red;">Error loading bookings</td></tr>`;
    return;
  }

  all.forEach(b=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${b.id}</td>
                 <td>${b.guest_name}</td>
                 <td>${b.room_type}</td>
                 <td>${b.check_in}</td>
                 <td>${b.check_out}</td>
                 <td>${b.price}</td>
                 <td>
                  <button class="action-btn" onclick="editBooking(${b.id})">Edit</button>
                  <button class="action-btn danger" onclick="deleteBooking(${b.id})">Delete</button>
                </td>`;
    tbody.appendChild(tr);
  });
}
// ----------------------------------------------------------


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


async function saveBookingToLocal(e){
  e.preventDefault();
  const id = document.getElementById('booking-id').value;
  const name = document.getElementById('guest-name').value.trim();
  const room = document.getElementById('room-type').value.trim();
  const checkin = document.getElementById('check-in').value;
  const checkout = document.getElementById('check-out').value;
  const price = Number(document.getElementById('price').value);

  if(new Date(checkout) < new Date(checkin)){
    alert('Check-out must be after check-in.');
    return false;
  }

  const arr = await getBookings();
  
  if (id) {
    const idx = arr.findIndex(x => x.id == id);
    if (idx >= 0) {
      arr[idx] = {
        id: Number(id),
        guest_name: name,
        room_type: room,
        check_in: checkin,
        check_out: checkout,
        price
      };
    }
  } else {
    const newId = arr.reduce((m, x) => Math.max(m, x.id || 0), 0) + 1;
    arr.push({
      id: newId,
      guest_name: name,
      room_type: room,
      check_in: checkin,
      check_out: checkout,
      price
    });
  }

  
  function saveBookings(arr) {
  localStorage.setItem('flashhotel_bookings', JSON.stringify(arr));
}
  hideBookingForm();
  renderBookings();
  return false;
}


async function editBooking(id) {
  const all = await getBookings();
  const b = all.find(x => x.id == id);
  if (!b) return;

  document.getElementById('booking-id').value = b.id;
  document.getElementById('guest-name').value = b.guest_name;
  document.getElementById('room-type').value = b.room_type;
  document.getElementById('check-in').value = b.check_in;
  document.getElementById('check-out').value = b.check_out;
  document.getElementById('price').value = b.price;

  document.getElementById('form-title').textContent = 'Edit Booking';
  document.getElementById('booking-form-section').style.display = 'block';
  document.getElementById('bookings-section').style.display = 'none';
}


async function deleteBooking(id){
  if(!confirm('Delete booking #' + id + '?')) return;
  const arr = await getBookings();
  const updated = arr.filter(x => x.id != id);
  saveBookings(updated);
  renderBookings();
}

async function getBookings() {
  try {
    const res = await fetch(`${API_BASE}/get_bookings.php`);
    return await res.json();
  } catch (err) {
    console.error("Fetch error:", err);
    return [];
  }
}

async function exportCSV(){
  const arr = await getBookings();
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


