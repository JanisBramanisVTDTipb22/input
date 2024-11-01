<?php
// dino-game.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dino Game</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #e0e0e0;
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
        }

        #gameCanvas {
            border: 1px solid #000;
            background-color: #f7f7f7;
            display: block;
            margin: 20px auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
        }

        #score {
            font-size: 24px;
            color: green; /* Set score color to green */
        }

        #restartButton {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #ff5722; /* Button color */
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            display: none; /* Hidden until game over */
        }

        #restartButton:hover {
            background-color: #e64a19; /* Darker shade on hover */
        }

        #volumeControl {
            margin-top: 20px;
            display: flex;
            align-items: center; /* Align items in the center */
        }

        #volume {
            width: 300px;
            cursor: pointer; /* Change cursor to indicate it's enabled */
        }

        #volumeLogo {
            width: 30px; /* Set the logo width */
            height: 30px; /* Set the logo height */
            margin-right: 10px; /* Space between logo and slider */
        }

        /* Style for the volume slider track and thumb */
        input[type="range"] {
            -webkit-appearance: none; /* Remove default styles */
            background: #ccc; /* Track color */
            height: 5px; /* Track height */
            border-radius: 5px; /* Round edges */
        }

        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none; /* Remove default styles */
            appearance: none; /* Remove default styles */
            width: 20px; /* Thumb width */
            height: 20px; /* Thumb height */
            border-radius: 50%; /* Round thumb */
            background: green; /* Thumb color */
            cursor: pointer; /* Cursor on hover */
        }

        input[type="range"]::-moz-range-thumb {
            width: 20px; /* Thumb width */
            height: 20px; /* Thumb height */
            border-radius: 50%; /* Round thumb */
            background: green; /* Thumb color */
            cursor: pointer; /* Cursor on hover */
        }
    </style>
</head>
<body>

<nav>
    <a href="brainrot.php">Brainrot Input</a>
    <a href="ksi-calendar.php">KSI Calendar Challenge</a>
    <a href="dino-game.php">Dino Game</a>
</nav>

<h1>Dino Game</h1>
<p>Press the space bar to start the game and jump!</p>
<canvas id="gameCanvas" width="600" height="200"></canvas>
<p>Score: <span id="score">0</span></p>
<button id="restartButton" onclick="restartGame()">Restart Game</button>

<div id="volumeControl">
    <img id="volumeLogo" src="volume-logo.png" alt="Volume Logo"> <!-- Add logo image -->
    <label for="volume">Volume:</label>
    <input type="range" id="volume" min="0" max="100" value="100" disabled>
</div>

<script>
    const canvas = document.getElementById('gameCanvas');
    const ctx = canvas.getContext('2d');
    const scoreDisplay = document.getElementById('score');
    const restartButton = document.getElementById('restartButton');

    let dino = { x: 50, y: 150, width: 40, height: 40, dy: 0, gravity: 0.5, jumpPower: 10, isJumping: false };
    let obstacles = [];
    let score = 0;
    let gameInterval;

    // Event listener for jump action
    document.addEventListener('keydown', (e) => {
        if (e.code === 'Space') {
            if (!gameInterval) {
                startGame(); // Start game on spacebar press
            }
            if (!dino.isJumping) {
                dino.isJumping = true;
                dino.dy = -dino.jumpPower;
            }
        }
    });

    function startGame() {
        gameInterval = setInterval(updateGame, 20);
        createObstacle();
    }

    function updateGame() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        drawDino();
        drawObstacles();
        updateObstacles();
        updateScore();
        checkCollision();

        dino.y += dino.dy;
        dino.dy += dino.gravity;

        if (dino.y >= canvas.height - dino.height) {
            dino.y = canvas.height - dino.height;
            dino.isJumping = false;
            dino.dy = 0;
        }
    }

    function drawDino() {
        const dinoImage = new Image();
        dinoImage.src = 'dino.png'; // Path to your dino image
        ctx.drawImage(dinoImage, dino.x, dino.y, dino.width, dino.height);
    }

    function createObstacle() {
        const obstacleImage = new Image();
        obstacleImage.src = 'cactus.png'; // Path to your cactus image
        const obstacleWidth = 30;
        const obstacleHeight = 40;
        const obstacleX = canvas.width;
        const obstacleY = canvas.height - obstacleHeight;
        obstacles.push({ x: obstacleX, y: obstacleY, width: obstacleWidth, height: obstacleHeight, img: obstacleImage });
        setTimeout(createObstacle, Math.random() * 2000 + 1000); // New obstacle every 1-3 seconds
    }

    function drawObstacles() {
        for (let obstacle of obstacles) {
            ctx.drawImage(obstacle.img, obstacle.x, obstacle.y, obstacle.width, obstacle.height);
        }
    }

    function updateObstacles() {
        for (let obstacle of obstacles) {
            obstacle.x -= 5; // Move obstacle to the left
        }
        obstacles = obstacles.filter(obstacle => obstacle.x + obstacle.width > 0); // Remove off-screen obstacles
    }

    function updateScore() {
        score++;
        scoreDisplay.textContent = score;
        adjustVolume(); // Adjust volume based on score

        // End game if score reaches 10,000
        if (score >= 10000) {
            endGameWithVolume();
        }
    }

    function adjustVolume() {
        const volume = calculateVolume(); // Get volume based on the current score
        document.getElementById('volume').value = volume * 100; // Update the volume slider
    }

    function calculateVolume() {
        return Math.min(score / 10000, 1); // Max volume at 10000 points
    }

    function setVolume(value) {
        // This function is no longer necessary since the slider is disabled
    }

    function checkCollision() {
        for (let obstacle of obstacles) {
            if (dino.x < obstacle.x + obstacle.width &&
                dino.x + dino.width > obstacle.x &&
                dino.y < obstacle.y + obstacle.height &&
                dino.y + dino.height > obstacle.y) {
                endGame();
            }
        }
    }

    function endGame() {
        clearInterval(gameInterval);
        alert('Skill issue!'); // Change the alert message to "Skill issue"
        restartButton.style.display = 'block';
    }

    function endGameWithVolume() {
        clearInterval(gameInterval);
        alert('Full volume reached!'); // Alert for reaching full volume
        restartButton.style.display = 'block';
    }

    function restartGame() {
        dino.y = 150;
        dino.dy = 0;
        dino.isJumping = false;
        obstacles = [];
        score = 0;
        scoreDisplay.textContent = score;
        restartButton.style.display = 'none';
        gameInterval = null; // Reset game interval
        startGame(); // Start the game again
    }

    // Start the game on page load
    window.onload = () => {
        document.addEventListener('keydown', (e) => {
            if (e.code === 'Space') {
                if (!gameInterval) {
                    startGame();
                }
            }
        });
    };
</script>

</body>
</html>
