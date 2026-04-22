<?php
session_start();
session_destroy();
session_unset();   

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Location: ../views/auth/login.php");
exit();     