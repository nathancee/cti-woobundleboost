<?php
define( 'CTI_LS', 1 );
require __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/auth.php';
Auth::logout();
header( 'Location: login.php' );
exit;
