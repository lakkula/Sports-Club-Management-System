<?php

$conn = mysqli_connect("localhost", "root", "", "sports_club_db");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>
