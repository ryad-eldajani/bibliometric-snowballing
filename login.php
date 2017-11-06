<?php
require 'src/autoload.php';

use \BS\User\UserManager;
$templates = new League\Plates\Engine('templates');

if (UserManager::instance()->checkCredentials($_POST['username'], $_POST['password'])) {
    echo $templates->render('projects', array('username' => $_POST['username']));
} else {
    echo $templates->render('login', array('message' => 'Login failed, username and password did not match.'));
}
