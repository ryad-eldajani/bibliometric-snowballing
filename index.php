<?php
require 'src/autoload.php';

var_export(\BS\User\UserManager::instance()->checkCredentials('testuser', 'testpass'));
