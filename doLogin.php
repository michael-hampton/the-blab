<?php
require 'models/Login.php';

$objLogin = new Login();

$blResult = $objLogin->login($_POST['username'], $_POST['password']);

if($blResult === FALSE) {
    echo 'ERROR';
    die;
}
