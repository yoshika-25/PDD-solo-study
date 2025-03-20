<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>YouTube Video in PHP</title>
</head>
<body>

    <h2>Embedded YouTube Video</h2>

    <?php
        $video_id = "https://youtu.be/H2EJuAcrZYU"; // Replace with your YouTube video ID
        echo "<iframe width='560' height='315' src='https://www.youtube.com/embed/H2EJuAcrZYU' frameborder='0' allowfullscreen></iframe>";
    ?>

</body>
</html>
