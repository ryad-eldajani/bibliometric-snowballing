<?php
require 'src/autoload.php';

// TODO: remove session etc.

$templates = new League\Plates\Engine('templates');
echo $templates->render('login');
