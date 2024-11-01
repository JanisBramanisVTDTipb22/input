<?php
// brainrot.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brainrot Input</title>
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

        #brainrotAlphabet {
            font-size: 18px;
            color: #444;
            margin-bottom: 10px;
        }

        #inputField {
            width: 300px;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        #submitButton {
            padding: 10px 20px;
            background-color: #ff5722; /* Button color */
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        #submitButton:hover {
            background-color: #e64a19; /* Darker shade on hover */
        }

        #output {
            margin-top: 20px;
            font-size: 16px;
            color: #444;
            padding: 10px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            display: none; /* Hidden until output is generated */
        }

        #error {
            color: red;
            margin-top: 10px;
            display: none; /* Hidden until there is an error */
        }
    </style>
</head>
<body>

<nav>
    <a href="brainrot.php">Brainrot Input</a>
    <a href="ksi-calendar.php">KSI Calendar Challenge</a>
    <a href="dino-game.php">Dino Game</a>
</nav>

<h1>Brainrot Input</h1>

<div id="brainrotAlphabet">
    <strong>Brainrot Alphabet:</strong><br>
    alpha: a 🐏<br>
    betamale: b 🐻<br>
    caseoh: c 🕵️‍♂️<br>
    drake: d 🦉<br>
    edging: e 🍑<br>
    fanumtax: f 💸<br>
    gyatt: g 🔥<br>
    heightmaxing: h 📏<br>
    ice_spice: i ❄️<br>
    jelqing: j 💪<br>
    ksi: k 🎤<br>
    logan_paul: l 🎬<br>
    mew: m 🐱<br>
    no_nut_november: n 🚫🥜<br>
    ohio: o 🏢<br>
    prime: p 👑<br>
    quandel_dingle: q ❓<br>
    rizz: r 😏<br>
    skibidi: s 🎶<br>
    toilet: t 🚽<br>
    uganda_knuckles: u 🐾<br>
    vector: v 📍<br>
    what_the_sigma!?!?!?: w 🤯<br>
    xqc: x 🎮<br>
    you_are_my_sunshine: y 🌞<br>
    zestyy_agdams: z 🍋<br>
</div>

<input type="text" id="inputField" placeholder="Type your message here..." oninput="validateInput()">
<button id="submitButton" onclick="generateBrainrot()">Submit</button>
<div id="error">Please only use brainrot terms!</div>
<div id="output"></div>

<script>
    const validTerms = {
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

    function validateInput() {
        const inputField = document.getElementById('inputField');
        const errorDiv = document.getElementById('error');
        const inputTerms = inputField.value.split(' ');

        // Check for any invalid term
        const hasInvalidTerm = inputTerms.some(term => !validTerms.hasOwnProperty(term) && term !== '');

        if (hasInvalidTerm) {
            errorDiv.style.display = 'block';
        } else {
            errorDiv.style.display = 'none';
        }
    }

    function generateBrainrot() {
        const input = document.getElementById('inputField').value;
        const outputDiv = document.getElementById('output');

        // Split the input by spaces to get the individual terms
        const inputTerms = input.split(' ');
        let brainrotMessage = '';
        let hasValidTerm = false; // Flag to check if there are valid terms

        // Convert brainrot terms back to letters
        for (let term of inputTerms) {
            if (validTerms.hasOwnProperty(term)) {
                brainrotMessage += validTerms[term]; // Append the corresponding letter
                hasValidTerm = true; // Found at least one valid term
            }
        }

        // Display output only if there are valid terms
        if (hasValidTerm) {
            outputDiv.textContent = brainrotMessage.trim();
            outputDiv.style.display = 'block'; // Show the output
        } else {
            outputDiv.style.display = 'none'; // Hide the output if no valid terms
        }
    }
</script>

</body>
</html>
