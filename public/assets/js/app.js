// app.js - handles availability checks and booking via AJAX
document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('bookingForm');
  const dateEl = document.getElementById('date');
  const timeEl = document.getElementById('time_slot');
  const seatsEl = document.getElementById('seats');
  const availabilityDiv = document.getElementById('availability');
  const messageDiv = document.getElementById('message');

  function showMessage(text, type = 'success') {
    messageDiv.textContent = text;
    messageDiv.className = 'message ' + (type === 'success' ? 'success' : 'error');
  }

  async function checkAvailability() {
    const date = dateEl.value;
    const time_slot = timeEl.value;
    const seats = seatsEl.value;
    availabilityDiv.textContent = 'Checking availability...';

    if (!date || !time_slot || !seats) {
      availabilityDiv.textContent = '';
      return;
    }

    try {
      const resp = await fetch('check_availability.php', {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({ date, time_slot, seats })
      });
      const data = await resp.json();
      if (data.available) {
        availabilityDiv.textContent = `Available tables: ${data.available_tables.join(', ')}`;
      } else {
        availabilityDiv.textContent = 'No tables available for that time/party size. Try another slot.';
      }
    } catch (err) {
      availabilityDiv.textContent = 'Error checking availability.';
      console.error(err);
    }
  }

  dateEl.addEventListener('change', checkAvailability);
  timeEl.addEventListener('change', checkAvailability);
  seatsEl.addEventListener('change', checkAvailability);

  form.addEventListener('submit', async function (e) {
    e.preventDefault();
    const formData = new FormData(form);
    showMessage('Submitting reservation...', 'success');

    try {
      const resp = await fetch('confirm.php', {
        method: 'POST',
        body: formData
      });
      const data = await resp.json();
      if (data.success) {
        showMessage('Reservation confirmed! Check your email for confirmation.', 'success');
        form.reset();
        availabilityDiv.textContent = '';
      } else {
        showMessage('Failed: ' + (data.message || 'Unknown error'), 'error');
      }
    } catch (err) {
      showMessage('Error while sending reservation.', 'error');
      console.error(err);
    }
  });
});