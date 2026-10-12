
<?php
session_start();
$_SESSION = [];
session_destroy();

header("Location: /food_waste/login.php");
exit;