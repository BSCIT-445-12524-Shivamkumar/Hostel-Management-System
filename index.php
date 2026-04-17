<?php
require_once 'includes/config.php';
if(isLoggedIn()) go(SITE_URL.'/'.$_SESSION['role'].'/dashboard.php');
else go(SITE_URL.'/home.php');
