<?php
session_start();
unset($_SESSION['token']);
unset($_SESSION['loggedin']);
session_destroy();

header("Location: ../loggin.php");
exit;
