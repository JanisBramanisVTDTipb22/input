<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Combined KSI Video and Dinosaur Game</title>
</head>
<body>

<main>
    <!-- KSI Video Section -->
    <div>
        <h1>KSI - Thick of It</h1>
        <iframe width="600" height="400" src="https://www.youtube.com/embed/7Hc7a9TQm9U" 
                title="KSI - Thick of It" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen>
        </iframe>
    </div>

    <!-- Dinosaur Game Section -->
    <div>
        <h2>Dinosaur Game</h2>
        <canvas id="gameCanvas" width="600" height="200"></canvas>
        <div>
            <input type="range" id="volumeSlider" min="0" max="100" value="100">
            <label for="volumeSlider">Volume</label>
            <button id="restartButton" class="hidden">Restart Game</button>
        </div>
    </div>
</main>

<script>
    // Dinosaur Game Script
    const canvas = document.getElementById('gameCanvas');
    const ctx = canvas.getContext('2d');
    let score = 0;
    let gameOver = false;
    let dinoY = canvas.height - 30;
    let gravity = 1;
    let isJumping = false;
    let jumpHeight = 50;
    let obstacles = [];
    let frame = 0;

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
                    if (dinoY >= canvas.height - 30) {
                        clearInterval(jumpInterval);
                        isJumping = false;
                    }
                }
                jumpCount++;
            }, 20);
        }
    }

    function createObstacle() {
        const obstacle = {
            x: canvas.width,
            y: canvas.height - 30,
            width: 20,
            height: 30
        };
        obstacles.push(obstacle);
    }

    function updateObstacles() {
        obstacles.forEach(obstacle => {
            obstacle.x -= 5;
            if (obstacle.x + obstacle.width < 0) {
                score += 100; // Increment score for passing the obstacle
                obstacles.shift(); // Remove off-screen obstacles
            }

            // Check for collision
            if (obstacle.x < 40 && obstacle.x + obstacle.width > 10 && dinoY + 30 >= obstacle.y) {
                gameOver = true;
            }
        });
    }

    function draw() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = '#6B8E23'; // Dino color
        ctx.fillRect(10, dinoY, 20, 30); // Draw the dinosaur

        obstacles.forEach(obstacle => {
            ctx.fillStyle = '#B22222'; // Obstacle color
            ctx.fillRect(obstacle.x, obstacle.y, obstacle.width, obstacle.height); // Draw obstacles
        });
    }

    function gameLoop() {
        if (!gameOver) {
            if (frame % 60 === 0) createObstacle(); // Create an obstacle every 60 frames
            updateObstacles();
            draw();
            score++;
            frame++;
            requestAnimationFrame(gameLoop);
        } else {
            ctx.fillText('Game Over', canvas.width / 2 - 30, canvas.height / 2);
            document.getElementById('restartButton').classList.remove('hidden');
        }
    }

    function restartGame() {
        score = 0;
        gameOver = false;
        dinoY = canvas.height - 30;
        obstacles = [];
        frame = 0;
        document.getElementById('restartButton').classList.add('hidden');
        gameLoop();
    }

    // Volume Slider Logic
    const volumeSlider = document.getElementById('volumeSlider');
    volumeSlider.addEventListener('input', () => {
        const volume = volumeSlider.value / 100; // Convert to a value between 0 and 1
        console.log(`Volume set to: ${volume * 100}%`);
        // You can add your audio handling code here to adjust the volume
    });

    // Start the game loop
    gameLoop();
</script>

</body>
</html>
