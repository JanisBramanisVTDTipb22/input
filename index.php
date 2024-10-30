<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Combined Video and Dinosaur Game</title>
    <style>
        #restartButton.hidden { display: none; }
    </style>
</head>
<body>

<main>
    <!-- New Video Section -->
    <div>
        <h1>Example Video</h1>
        <iframe id="video" width="600" height="400" src="https://www.youtube.com/watch?v=vhpxylukBxo" 
                title="Example Video" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen>
        </iframe>
    </div>

    <!-- Dinosaur Game Section -->
    <div>
        <h2>Dinosaur Game</h2>
        <canvas id="gameCanvas" width="600" height="200" aria-label="Dinosaur game canvas"></canvas>
        <button id="restartButton" class="hidden" aria-label="Restart Game">Restart Game</button>
        <div>
            <label>Score: <span id="scoreDisplay">0</span></label>
            <br>
            <label>Volume: <progress id="volumeBar" value="0" max="100"></progress></label>
        </div>
    </div>
</main>

<script>
    const canvas = document.getElementById('gameCanvas');
    const ctx = canvas.getContext('2d');
    let score = 0;
    let gameOver = false;
    let dinoY = canvas.height - 50;
    let isJumping = false;
    let jumpHeight = 30; // Slightly higher jump height
    let jumpSpeed = 10; // Fast jump speed
    let obstacles = [];
    let frame = 0;

    // Load the dino.png and cactus.png images
    const dinoImage = new Image();
    dinoImage.src = 'dino.png'; // Ensure 'dino.png' is in the same directory or adjust path

    const cactusImage = new Image();
    cactusImage.src = 'cactus.png'; // Ensure 'cactus.png' is in the same directory or adjust path

    // YouTube API for video volume control
    let player;
    function onYouTubeIframeAPIReady() {
        player = new YT.Player('video', {
            events: {
                'onReady': onPlayerReady,
            }
        });
    }

    function onPlayerReady(event) {
        adjustVolumeByScore();
    }

    // Adjust volume based on score
    function adjustVolumeByScore() {
        const volume = Math.min(score, 100); // Volume range from 0 to 100 based on score
        if (player && player.setVolume) {
            player.setVolume(volume); // Set YouTube volume
        }
        document.getElementById('volumeBar').value = volume; // Update volume bar
    }

    // Draw the dinosaur image at the player's position
    function drawDino(x, y) {
        ctx.drawImage(dinoImage, x, y, 40, 40); // Draw dino.png at player position with size 40x40
    }

    // Draw cactus image for obstacles
    function drawCactus(x, y) {
        ctx.drawImage(cactusImage, x, y, 40, 50); // Draw cactus.png larger (40x50)
    }

    // Jump functionality
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
                    dinoY -= 5; // Move up
                } else {
                    dinoY += 5; // Move down
                    if (dinoY >= canvas.height - 50) {
                        clearInterval(jumpInterval);
                        isJumping = false;
                    }
                }
                jumpCount++;
            }, jumpSpeed); // Faster interval for quicker jumps
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
            // Check for collision
            if (obstacle.x < 40 && obstacle.x + obstacle.width > 10 && dinoY + 40 >= obstacle.y) {
                gameOver = true;
            }
            // Score points if jumping over an obstacle
            if (obstacle.x + obstacle.width < 10 && obstacle.scored !== true) {
                obstacle.scored = true; // Mark as scored
                if (score < 1000) { // Ensure score does not exceed 1000
                    score += 1; // Increase score by 1 for each jump over an obstacle
                    document.getElementById('scoreDisplay').textContent = score; // Update score display
                    adjustVolumeByScore(); // Update volume based on new score
                }
            }
            if (obstacle.x + obstacle.width < 0) {
                obstacles.splice(index, 1); // Remove off-screen obstacles
            }
        });

        // End game if score reaches 1000
        if (score >= 1000) {
            gameOver = true;
        }
    }

    function draw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        drawDino(10, dinoY); // Draw the dino.png image for the player character

        obstacles.forEach(obstacle => {
            drawCactus(obstacle.x, obstacle.y); // Draw cactus.png for obstacles
        });

        if (gameOver) {
            ctx.textAlign = 'center';
            ctx.font = '20px Arial';
            ctx.fillStyle = '#000';
            ctx.fillText('Game Over', canvas.width / 2, canvas.height / 2);
            ctx.fillText('Final Score: ' + score, canvas.width / 2, canvas.height / 2 + 20); // Show final score
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
        document.getElementById('scoreDisplay').textContent = score; // Reset score display
        document.getElementById('volumeBar').value = 0; // Reset volume bar
        document.getElementById('restartButton').classList.add('hidden');
        gameLoop();
    }

    // Initialize game loop
    gameLoop();
</script>

<script src="https://www.youtube.com/iframe_api"></script>
</body>
</html>
