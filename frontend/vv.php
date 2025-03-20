<?php
session_start(); // Start the session
include '../Backend/db.php'; // Database connection

// Retrieve video timestamp
$user_id = $_SESSION['user_id']; // Replace with the logged-in user ID
$query = "SELECT timestamp FROM video_progress WHERE Signup_id = :user_id";

$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$start_timestamp = $row ? $row['timestamp'] : 0;

// Retrieve video title from session
$video_title = $_SESSION['selected_subject'] ?? 'default_video_title';

// Fetch video URL from videos table based on title
$query = "SELECT video_url FROM videos WHERE title = :video_title";
$stmt = $conn->prepare($query);
$stmt->bindParam(':video_title', $video_title, PDO::PARAM_STR);
$stmt->execute();
$video_row = $stmt->fetch(PDO::FETCH_ASSOC);
$video_url = $video_row ? $video_row['video_url'] : 'H2EJuAcrZYU'; // Default video ID if not found
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Timer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #00A5C0, #4CAF50);
            color: white;
            font-family: 'Arial', sans-serif;
            min-height: 100vh;
        }
        .container {
            max-width: 900px;
            margin: auto;
            text-align: center;
            padding-top: 100px;
        }
        .timer-box {
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 25px;
            border-radius: 15px;
            font-size: 24px;
            font-weight: bold;
            margin: 10px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        .btn-pause, .btn-stop {
            border: none;
            padding: 12px 30px;
            font-size: 18px;
            border-radius: 50px;
            cursor: pointer;
            margin: 10px;
            color: white;
        }
        .btn-pause {
            background: linear-gradient(45deg, #ff4b4b, #ff6b6b);
        }
        .btn-stop {
            background: linear-gradient(45deg, #007BFF, #00A5C0);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="timer-box">
            <span>Total Time:</span>
            <span id="totalHours">00:00:00</span>
        </div>
        <div class="timer-box">
            <span>Remaining Time:</span>
            <span id="remainingTime">00:00:00</span>
        </div>

        <div id="youtube-player"></div>

        <button class="btn-pause" onclick="togglePause()">Pause</button>
        <button class="btn-stop" onclick="stopVideo()">Stop</button>
    </div>

    <div class="modal fade" id="completionModal" tabindex="-1" aria-labelledby="completionModalLabel" aria-hidden="true">
        <div class="modal-dialog text-dark">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Task Completed!</h5>
                </div>
                <div class="modal-body">
                    Your task is completed for today.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="okBtn">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let totalTime = localStorage.getItem("dailyStudyTime") ? parseFloat(localStorage.getItem("dailyStudyTime")) * 60 : 0;
        let player;
        let isPaused = false;
        let timerInterval;
        let videoDuration = 0;
        let startTimestamp = <?php echo $start_timestamp; ?>;

        function formatTime(seconds) {
            let hours = Math.floor(seconds / 3600);
            let minutes = Math.floor((seconds % 3600) / 60);
            let secs = Math.floor(seconds % 60);
            return String(hours).padStart(2, '0') + ':' + String(minutes).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
        }

        function updateTimerDisplay() {
            document.getElementById("totalHours").innerText = formatTime(totalTime);
            document.getElementById("remainingTime").innerText = formatTime(videoDuration - player.getCurrentTime());
        }

        function updateTimers() {
            if (!isPaused && player && player.getPlayerState() === YT.PlayerState.PLAYING) {
                if (totalTime > 0) {
                    totalTime--;
                    updateTimerDisplay();
                } else {
                    player.pauseVideo();
                    showCompletionDialog();
                    clearInterval(timerInterval);
                }
            }
        }

        function showCompletionDialog() {
            let modal = new bootstrap.Modal(document.getElementById('completionModal'));
            modal.show();
            document.getElementById("okBtn").onclick = function () {
                modal.hide();
                window.location.href = "index.html";
            };
        }

        function startTimerInterval() {
            if (timerInterval) clearInterval(timerInterval);
            timerInterval = setInterval(updateTimers, 1000);
        }

        function updateVideoProgress() {
            if (player) {
                let currentTime = document.getElementById("remainingTime").innerText;
                let xhr = new XMLHttpRequest();
                xhr.open("POST", "update_progress.php", true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        console.log("Progress updated: " + currentTime);
                    }
                };
                xhr.send("timestamp=" + currentTime);
            }
        }

        function togglePause() {
            if (isPaused) {
                player.playVideo();
                isPaused = false;
                startTimerInterval();
            } else {
                player.pauseVideo();
                isPaused = true;
                clearInterval(timerInterval);
                updateVideoProgress();
            }
        }

        function stopVideo() {
            player.stopVideo();
            clearInterval(timerInterval);
            updateVideoProgress();
        }

        function loadYouTubeAPI() {
            let script = document.createElement("script");
            script.src = "https://www.youtube.com/iframe_api";
            document.body.appendChild(script);
        }

        function onYouTubeIframeAPIReady() {
            player = new YT.Player('youtube-player', {
                height: '360',
                width: '640',
                videoId: '<?php echo $video_url; ?>', // Use the video URL fetched from the database
                playerVars: {
                    'controls': 0,
                    'disablekb': 1,
                    'modestbranding': 1,
                    'rel': 0,
                    'fs': 0,
                    'iv_load_policy': 3,
                    'autoplay': 1,
                    'mute': 0,
                    'playsinline': 1,
                    'showinfo': 0,
                    'start': startTimestamp
                },
                events: {
                    'onReady': function (event) {
                        videoDuration = player.getDuration();
                        updateTimerDisplay();
                        startTimerInterval();
                        event.target.playVideo();
                    },
                    'onStateChange': function (event) {
                        isPaused = event.data !== YT.PlayerState.PLAYING;
                        document.querySelector(".btn-pause").innerText = isPaused ? "Resume" : "Pause";
                    }
                }
            });
        }

        loadYouTubeAPI();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>