const API_URL = "http://127.0.0.1:8000/api/bookings";

async function getBookings() {
  const res = await fetch(API_URL);
  return await res.json();
}

async function saveBooking(e){
  e.preventDefault();
  const data = {
    name: document.getElementById('guest-name').value,
    room: document.getElementById('room-type').value,
    checkin: document.getElementById('check-in').value,
    checkout: document.getElementById('check-out').value,
    price: Number(document.getElementById('price').value)
  };

  const res = await fetch(API_URL, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data)
  });

  if(res.ok){
    alert("Booking saved to MySQL!");
    hideBookingForm();
    renderBookings();
  } else {
    const err = await res.json();
    alert("Error: " + err.error);
  }
}

async function renderBookings(){
  const tbody = document.querySelector('#bookings-table tbody');
  tbody.innerHTML = '';
  const all = await getBookings();
  all.forEach(b=>{
    const tr = document.createElement('tr');
    tr.innerHTML = `<td>${b.id}</td>
                    <td>${b.guest_name}</td>
                    <td>${b.room_type}</td>
                    <td>${b.check_in}</td>
                    <td>${b.check_out}</td>
                    <td>${b.price}</td>`;
    tbody.appendChild(tr);
  });
}
