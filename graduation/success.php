<?php
session_start();
if (isset($_SESSION['success_message'])) {
    echo $_SESSION['success_message'];
    unset($_SESSION['success_message']); // Clear the message after displaying
} else {
    echo "No message available.";
}
?>
