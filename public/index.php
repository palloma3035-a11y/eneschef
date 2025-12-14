<?php
require_once __DIR__ . '/../config.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Restaurant Booking</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="container">
    <header>
      <h1>Book a Table</h1>
    </header>

    <main>
      <form id="bookingForm">
        <label>
          Name
          <input name="name" required>
        </label>

        <label>
          Email
          <input name="email" type="email" required>
        </label>

        <label>
          Phone
          <input name="phone">
        </label>

        <label>
          Date
          <input name="date" id="date" type="date" required min="<?= date('Y-m-d') ?>">
        </label>

        <label>
          Time
          <select name="time_slot" id="time_slot" required>
            <option value="">Select time</option>
            <option>12:00</option>
            <option>12:30</option>
            <option>13:00</option>
            <option>18:00</option>
            <option>18:30</option>
            <option>19:00</option>
            <option>19:30</option>
            <option>20:00</option>
          </select>
        </label>

        <label>
          Seats
          <select name="seats" id="seats" required>
            <option>1</option>
            <option>2</option>
            <option>3</option>
            <option>4</option>
            <option>5</option>
            <option>6</option>
          </select>
        </label>

        <div id="availability"></div>

        <button type="submit">Confirm Reservation</button>
      </form>
      <div id="message" class="message"></div>
    </main>

    <footer>
      created by enestahiri.com
    </footer>
  </div>

  <script src="assets/js/app.js"></script>
</body>
</html>