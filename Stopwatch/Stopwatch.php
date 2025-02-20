<?php
// stopwatch.php

// This PHP script will just render the HTML and JavaScript for the stopwatch.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stopwatch</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
        }
        #stopwatch {
            font-size: 48px;
            margin-bottom: 20px;
        }
        button {
            font-size: 18px;
            padding: 10px 20px;
            margin: 5px;
        }
    </style>
</head>
<body>

    <h1>Stopwatch</h1>
    <div id="stopwatch">00:00:00</div>
    <button id="start">Start</button>
    <button id="stop">Stop</button>
    <button id="reset">Reset</button>

    <script>
        // JavaScript for Stopwatch Functionality
        let startTime = 0; // Stores the start time
        let elapsedTime = 0; // Tracks elapsed time
        let intervalId = null; // Stores the interval ID

        const stopwatchElement = document.getElementById('stopwatch');
        const startButton = document.getElementById('start');
        const stopButton = document.getElementById('stop');
        const resetButton = document.getElementById('reset');

        // Function to format time as HH:MM:SS
        function formatTime(seconds) {
            const hours = Math.floor(seconds / 3600).toString().padStart(2, '0');
            const minutes = Math.floor((seconds % 3600) / 60).toString().padStart(2, '0');
            const secs = Math.floor(seconds % 60).toString().padStart(2, '0');
            return `${hours}:${minutes}:${secs}`;
        }

        // Function to update the stopwatch display
        function updateStopwatch() {
            elapsedTime = Date.now() - startTime;
            stopwatchElement.textContent = formatTime(Math.floor(elapsedTime / 1000));
        }

        // Start the stopwatch
        startButton.addEventListener('click', () => {
            if (!intervalId) {
                startTime = Date.now() - elapsedTime;
                intervalId = setInterval(updateStopwatch, 100); // Update every 100ms
            }
        });

        // Stop the stopwatch
        stopButton.addEventListener('click', () => {
            clearInterval(intervalId);
            intervalId = null;
        });

        // Reset the stopwatch
        resetButton.addEventListener('click', () => {
            clearInterval(intervalId);
            intervalId = null;
            elapsedTime = 0;
            stopwatchElement.textContent = '00:00:00';
        });
    </script>

</body>
</html>