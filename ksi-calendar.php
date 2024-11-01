<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KSI Calendar Challenge</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f0f4f8;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        nav {
            margin-bottom: 20px;
            background-color: #333;
            padding: 10px 0;
            width: 100%;
        }

        nav a {
            margin: 0 15px;
            text-decoration: none;
            color: #fff;
            font-weight: bold;
        }

        nav a:hover {
            text-decoration: underline;
        }

        h1 {
            color: #444;
            margin: 20px 0;
        }

        label {
            font-size: 18px;
            color: #333;
            margin: 10px 0;
        }

        input[type="date"] {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 20px;
            width: 250px;
        }

        #startButton {
            padding: 10px 20px;
            background-color: #ff5722; /* Button color */
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        #startButton:hover {
            background-color: #e64a19; /* Darker shade on hover */
        }

        #progress {
            margin: 20px 0;
            font-size: 16px;
            color: #444;
        }

        #completionMessage {
            display: none;
            margin-top: 20px;
            font-size: 18px;
            color: #28a745; /* Green color for success */
        }

        table {
            margin: 20px auto;
            border-collapse: collapse;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            width: 40px;
            height: 40px;
            text-align: center;
        }

        .marked {
            background-color: lightgreen;
        }

        .selected {
            background-color: lightblue; /* Highlight for selected date */
        }

        video {
            margin: 20px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        #volumeControl {
            margin: 20px 0;
            display: flex;
            align-items: center; /* Align items in the center */
        }

        #volume {
            width: 300px; /* Set width of the slider */
            margin-left: 10px; /* Add space between label and slider */
        }
    </style>
</head>
<body>

<nav>
    <a href="brainrot.php">Brainrot Input</a>
    <a href="ksi-calendar.php">KSI Calendar Challenge</a>
    <a href="dino-game.php">Dino Game</a>
</nav>

<h1>KSI Calendar Challenge</h1>
<p>To mark a date on the calendar, watch the video continuously without pausing or skipping!</p>

<label for="targetDate">Select a date you want to mark:</label>
<input type="date" id="targetDate" aria-label="Select a date">
<button id="startButton" onclick="startChallenge()">Start Challenge</button>

<div>
    <video id="ksiVideo" width="600" autoplay muted>
        <source src="thick.of.it.mp4" type="video/mp4"> <!-- Updated video source -->
        Your browser does not support the video tag.
    </video>
</div>

<div id="volumeControl">
    <label for="volume">Volume:</label>
    <input type="range" id="volume" min="0" max="100" value="100" oninput="setVolume(this.value)">
</div>

<div id="progress">
    <p>Plays required: <span id="requiredPlays">0</span></p>
    <p>Current plays: <span id="currentPlays">0</span></p>
</div>

<div id="completionMessage">
    <h2>Congratulations! You've marked this day on the calendar.</h2>
</div>

<h2>Calendar</h2>
<table id="calendarTable">
    <thead>
        <tr>
            <th>Sun</th>
            <th>Mon</th>
            <th>Tue</th>
            <th>Wed</th>
            <th>Thu</th>
            <th>Fri</th>
            <th>Sat</th>
        </tr>
    </thead>
    <tbody id="calendarBody"></tbody>
</table>

<script>
    const video = document.getElementById('ksiVideo');
    const targetDateInput = document.getElementById('targetDate');
    const startButton = document.getElementById('startButton');
    const requiredPlaysDisplay = document.getElementById('requiredPlays');
    const currentPlaysDisplay = document.getElementById('currentPlays');
    const completionMessage = document.getElementById('completionMessage');

    let requiredPlays = 0;
    let currentPlays = 0;
    let markedDates = new Set();

    function calculatePlays() {
        const targetDate = new Date(targetDateInput.value);
        const today = new Date();
        const timeDiff = targetDate - today;

        if (timeDiff <= 0) {
            alert("Please select a future date.");
            return;
        }

        const dayDifference = Math.ceil(timeDiff / (1000 * 60 * 60 * 24));
        requiredPlays = dayDifference;
        requiredPlaysDisplay.textContent = requiredPlays;
        currentPlays = 0;
        currentPlaysDisplay.textContent = currentPlays;

        highlightSelectedDate(targetDate);
    }

    function highlightSelectedDate(date) {
        // Reset all previous highlights
        const cells = document.querySelectorAll('#calendarBody td');
        cells.forEach(cell => {
            cell.classList.remove('selected');
        });

        // Highlight the selected date
        const cellDateString = date.toISOString().split('T')[0];
        const targetDay = date.getDate();
        const month = date.getMonth();
        const year = date.getFullYear();
        const cellToHighlight = Array.from(cells).find(cell => {
            const cellDate = new Date(year, month, cell.textContent);
            return cellDate.toISOString().split('T')[0] === cellDateString;
        });

        if (cellToHighlight) {
            cellToHighlight.classList.add('selected');
        }
    }

    function startChallenge() {
        if (!targetDateInput.value) {
            alert("Please select a target date first.");
            return;
        }

        calculatePlays();
        if (requiredPlays > 0) {
            startButton.disabled = true;
            completionMessage.style.display = "none";
            video.currentTime = 0;
            video.play();
            video.controls = false; // Disable video controls
        }
    }

    video.addEventListener('ended', () => {
        currentPlays++;
        currentPlaysDisplay.textContent = currentPlays;

        if (currentPlays < requiredPlays) {
            alert("Click play to start the next listen.");
            video.currentTime = 0; // Reset video for the next play
            video.play(); // Automatically play again
        } else {
            const targetDate = targetDateInput.value;
            markedDates.add(targetDate);
            updateCalendar();
            startButton.disabled = false;

            // Popup message
            alert("Skill issue, lil bro!");

            // Hide calendar
            document.getElementById('calendarTable').style.display = 'none';
        }
    });

    function updateCalendar() {
        const targetDate = new Date(targetDateInput.value);
        const cellDateString = targetDate.toISOString().split('T')[0];
        const targetDay = targetDate.getDate();
        const month = targetDate.getMonth();
        const year = targetDate.getFullYear();

        // Mark the date on the calendar
        const cells = document.querySelectorAll('#calendarBody td');
        cells.forEach(cell => {
            const cellDate = new Date(year, month, cell.textContent);
            if (cellDate.toISOString().split('T')[0] === cellDateString) {
                cell.classList.add('marked'); // Mark as completed
            }
        });

        // Show completion message
        completionMessage.style.display = "block";
    }

    function generateCalendar() {
        const calendarBody = document.getElementById('calendarBody');
        const currentDate = new Date();
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();

        const firstDayIndex = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        let row = document.createElement('tr');
        for (let i = 0; i < firstDayIndex; i++) {
            row.appendChild(document.createElement('td'));
        }

        for (let day = 1; day <= daysInMonth; day++) {
            const cell = document.createElement('td');
            cell.textContent = day;
            const cellDate = new Date(year, month, day);
            const cellDateString = cellDate.toISOString().split('T')[0];

            if (markedDates.has(cellDateString)) {
                cell.classList.add('marked');
            }

            row.appendChild(cell);
            if ((day + firstDayIndex) % 7 === 0) {
                calendarBody.appendChild(row);
                row = document.createElement('tr');
            }
        }

        if (row.children.length > 0) {
            calendarBody.appendChild(row);
        }
    }

    function setVolume(value) {
        video.volume = value / 100; // Set volume based on slider value (0 to 1)
    }

    // Set video to full volume at the beginning
    video.volume = 1; // Set video volume to 100%

    // Call the function to generate the calendar on page load
    window.onload = generateCalendar;
</script>

</body>
</html>
