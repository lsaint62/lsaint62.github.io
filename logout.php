<?php
// deconnexion.php

session_start();
session_unset();
session_destroy();
header('Location: test.html');
exit();
?>
