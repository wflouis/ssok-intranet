<?php

session_start();
session_destroy();
setcookie('intranet-token', null, -1); 

header('location: login.php');
