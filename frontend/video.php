<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Embedded YouTube Video</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .video-container {
            position: relative;
            width: 80%;
            max-width: 560px;
            margin: auto;
            overflow: hidden;
            padding-top: 56.25%; /* 16:9 Aspect Ratio */
        }
        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>
</head>
<body>

    <h2>Watch this YouTube Video</h2>

    <div class="video-container">
        echo "<iframe width='560' height='315' src='https://www.youtube.com/embed/H2EJuAcrZYU' allowfullscreen></iframe>";
        
    </div>

</body>
</html>
