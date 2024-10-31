<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KSI Thick of It Calendar Challenge with Dinosaur Game</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; }
        #progress { margin: 20px 0; }
        #restartButton.hidden { display: none; }
        #gameCanvas { margin-top: 20px; }
        table { margin: 20px auto; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #ddd; width: 40px; height: 40px; }
        .marked { background-color: lightgreen; }
    </style>
</head>
<body>

<h1>Calendar Challenge</h1>
<p>To mark a date on the calendar, watch the video continuously without pausing or skipping!</p>

<!-- Date input -->
<label for="targetDate">Select a date you want to mark:</label>
<input type="date" id="targetDate" aria-label="Select a date">
<button id="startButton" onclick="startChallenge()">Start Challenge</button>

<!-- Video Player -->
<div>
    <video id="ksiVideo" width="600" controls muted>
        <source src="thick.of.it.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>
</div>

<!-- Progress Display -->
<div id="progress">
    <p>Plays required: <span id="requiredPlays">0</span></p>
    <p>Current plays: <span id="currentPlays">0</span></p>
</div>

<!-- Completion Message -->
<div id="completionMessage" style="display: none;">
    <h2>Congratulations! You've marked this day on the calendar.</h2>
</div>

<!-- Calendar Display -->
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

<!-- Dinosaur Game Section -->
<h2>Dinosaur Game</h2>
<canvas id="gameCanvas" width="600" height="200" aria-label="Dinosaur game canvas"></canvas>
<button id="restartButton" class="hidden" aria-label="Restart Game">Restart Game</button>
<div>
    <label>Score: <span id="scoreDisplay">0</span></label>
    <br>
    <label>Volume: <progress id="volumeBar" value="0" max="100"></progress></label>
</div>

<script>
    // Video Challenge Logic
    const video = document.getElementById('ksiVideo');
    const targetDateInput = document.getElementById('targetDate');
    const startButton = document.getElementById('startButton');
    const requiredPlaysDisplay = document.getElementById('requiredPlays');
    const currentPlaysDisplay = document.getElementById('currentPlays');
    const completionMessage = document.getElementById('completionMessage');
    
    let requiredPlays = 0;
    let currentPlays = 0;
    let markedDates = new Set(); // Store marked dates

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
        }
    }

    video.addEventListener('ended', () => {
        currentPlays++;
        currentPlaysDisplay.textContent = currentPlays;

        if (currentPlays < requiredPlays) {
            alert("Click play to start the next listen.");
        } else {
            const targetDate = targetDateInput.value;
            markedDates.add(targetDate); // Add date to marked dates
            console.log(`Marking date: ${targetDate}`); // Log the date marking
            updateCalendar();
            startButton.disabled = false;
            completionMessage.style.display = "block";
        }
    });

    // Calendar Generation
    function generateCalendar() {
        const calendarBody = document.getElementById('calendarBody');
        const currentDate = new Date();
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();

        // Calculate the first day of the month and days in the month
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

            // Log the current cell date being processed
            console.log(`Processing cell date: ${cellDateString}`); 

            if (markedDates.has(cellDateString)) {
                cell.classList.add('marked');
            }

            row.appendChild(cell);
            if ((day + firstDayIndex) % 7 === 0 || day === daysInMonth) {
                calendarBody.appendChild(row);
                row = document.createElement('tr');
            }
        }
    }

    function updateCalendar() {
        document.getElementById('calendarBody').innerHTML = '';
        generateCalendar();
    }

    generateCalendar();

    // Dinosaur Game Logic
    const canvas = document.getElementById('gameCanvas');
    const ctx = canvas.getContext('2d');
    let score = 0;
    let gameOver = false;
    let dinoY = canvas.height - 50;
    let isJumping = false;
    let jumpHeight = 30;
    let jumpSpeed = 10;
    let obstacles = [];
    let frame = 0;

    const dinoImage = new Image();
    dinoImage.src = 'dino.png'; // Make sure you have a dino.png image

    const cactusImage = new Image();
    cactusImage.src = 'cactus.png'; // Make sure you have a cactus.png image

    function drawDino(x, y) {
        ctx.drawImage(dinoImage, x, y, 40, 40);
    }

    function drawCactus(x, y) {
        ctx.drawImage(cactusImage, x, y, 40, 50);
    }

    document.addEventListener('keydown', function(event) {
        if (event.code === 'Space') {
            if (gameOver) {
                restartGame();
            } else {
                jump();
            }
        }
    });

    function jump() {
        if (!isJumping) {
            isJumping = true;
            let jumpCount = 0;
            const jumpInterval = setInterval(() => {
                if (jumpCount < jumpHeight) {
                    dinoY -= 5;
                } else {
                    dinoY += 5;
                    if (dinoY >= canvas.height - 50) {
                        clearInterval(jumpInterval);
                        isJumping = false;
                    }
                }
                jumpCount++;
            }, jumpSpeed);
        }
    }

    function createObstacle() {
        const obstacle = {
            x: canvas.width,
            y: canvas.height - 50,
            width: 40,
            height: 50
        };
        obstacles.push(obstacle);
    }

    function updateObstacles() {
        obstacles.forEach((obstacle, index) => {
            obstacle.x -= 5;
            if (obstacle.x < 40 && obstacle.x + obstacle.width > 10 && dinoY + 40 >= obstacle.y) {
                gameOver = true;
            }
            if (obstacle.x + obstacle.width < 10 && obstacle.scored !== true) {
                obstacle.scored = true;
                score += 1;
                document.getElementById('scoreDisplay').textContent = score;
            }
            if (obstacle.x + obstacle.width < 0) {
                obstacles.splice(index, 1);
            }
        });
    }

    function draw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        drawDino(10, dinoY);
        obstacles.forEach(obstacle => {
            drawCactus(obstacle.x, obstacle.y);
        });
        if (gameOver) {
            ctx.textAlign = 'center';
            ctx.font = '20px Arial';
            ctx.fillStyle = '#000';
            ctx.fillText('Game Over', canvas.width / 2, canvas.height / 2);
        }
    }

    function gameLoop() {
        if (!gameOver) {
            if (frame % 60 === 0) createObstacle();
            updateObstacles();
            draw();
            frame++;
            requestAnimationFrame(gameLoop);
        } else {
            document.getElementById('restartButton').classList.remove('hidden');
        }
    }

    function restartGame() {
        score = 0;
        gameOver = false;
        dinoY = canvas.height - 50;
        obstacles = [];
        frame = 0;
        document.getElementById('scoreDisplay').textContent = score;
        document.getElementById('restartButton').classList.add('hidden');
        gameLoop();
    }

    gameLoop();
</script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brainrot Word Input</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 20px;
        }
        #brainrotInput {
            width: 300px;
            height: 100px;
        }
        #errorMessage {
            color: red;
            margin-top: 10px;
        }
        #output {
            margin-top: 20px;
        }
        .brainrot-word {
            text-align: center; /* Center the text */
            font-size: 1.5em; /* Increase the font size */
        }
    </style>
</head>
<body>

<h1>Brainrot Word Input</h1>
<p>Type your message using brainrot words corresponding to each letter.</p>

<!-- Brainrot Words Dictionary -->
<div>
    <strong>Brainrot Words:</strong>
    <div class="brainrot-word">
        a: alpha 🐺<br>
        b: betamale 🐢<br>
        c: caseoh 🍔<br>
        d: drake 🎤<br>
        e: edging 🍆<br>
        f: fanumtax 🌊<br>
        g: gyatt 🚀<br>
        h: heightmaxing ⛰️<br>
        i: ice_spice ❄️<br>
        j: jelqing 🍑<br>
        k: ksi 🥊<br>
        l: logan_paul 🎥<br>
        m: mew 🐱<br>
        n: no_nut_november 🚫🥜<br>
        o: ohio 🌪️<br>
        p: prime 🌟<br>
        q: quandel_dingle 🔍<br>
        r: rizz 💖<br>
        s: skibidi 💃<br>
        t: toilet 🚽<br>
        u: uganda_knuckles 🐸<br>
        v: vector 🎮<br>
        w: what_the_sigma!?!?!? 🤨<br>
        x: xqc 💻<br>
        y: you_are_my_sunshine ☀️<br>
        z: zestyy_agdams 🥤<br>
    </div>
</div>

<!-- Input Field for Brainrot Words -->
<textarea id="brainrotInput" placeholder="Type your message..."></textarea>
<button onclick="submitMessage()">Submit</button>

<!-- Error Message Display -->
<div id="errorMessage"></div>

<!-- Output Display -->
<div id="output"></div>

<script>
    const brainrotDictionary = {
        'alpha': 'a',
        'betamale': 'b',
        'caseoh': 'c',
        'drake': 'd',
        'edging': 'e',
        'fanumtax': 'f',
        'gyatt': 'g',
        'heightmaxing': 'h',
        'ice_spice': 'i',
        'jelqing': 'j',
        'ksi': 'k',
        'logan_paul': 'l',
        'mew': 'm',
        'no_nut_november': 'n',
        'ohio': 'o',
        'prime': 'p',
        'quandel_dingle': 'q',
        'rizz': 'r',
        'skibidi': 's',
        'toilet': 't',
        'uganda_knuckles': 'u',
        'vector': 'v',
        'what_the_sigma!?!?!?': 'w',
        'xqc': 'x',
        'you_are_my_sunshine': 'y',
        'zestyy_agdams': 'z'
    };

    function submitMessage() {
        const inputField = document.getElementById('brainrotInput');
        const errorMessage = document.getElementById('errorMessage');
        const output = document.getElementById('output');
        const inputText = inputField.value.toLowerCase().trim();
        
        // Clear previous messages
        errorMessage.textContent = '';
        output.textContent = '';

        // Split the input by space for different words
        const words = inputText.split(/\s+/);
        const isValid = words.every(word => {
            // Check if the word is a valid brainrot word
            return Object.keys(brainrotDictionary).includes(word);
        });

        if (isValid) {
            // Get the first letters of each brainrot word
            const firstLetters = words.map(word => {
                return brainrotDictionary[word].toUpperCase();
            }).join(' '); // Join different words with a space
            output.textContent = 'Your brainrot message: ' + firstLetters;
            inputField.value = ''; // Clear input field after submission
        } else {
            errorMessage.textContent = 'You can only use brainrot words!';
        }
    }
</script>

</body>
</html>
