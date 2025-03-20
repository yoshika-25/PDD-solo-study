<?php
// evaluation.php
if (isset($_GET['endDate'])) {
    $endDate = $_GET['endDate'];
    $systemDate = date("Y-m-d");

    if ($endDate === $systemDate) {
        echo "<h1>Evaluation Question</h1>";
        echo "<p>1. What is the output of the following Python code: <code>print(2 + 3)</code>?</p>";
    } else {
        echo "<h1>Access Denied</h1>";
        echo "<p>The date does not match today's system date. Please try again.</p>";
    }
} else {
    echo "<h1>Invalid Access</h1>";
    echo "<p>No date provided. Please go back and enter the date.</p>";
}
?>
