<?php

require_once 'models/UserFactory.php';

$objUserFactory = new UserFactory();

$user_name = strtolower($_POST['firstname']) . '.' . strtolower($_POST['lastname']);
$user_name = str_replace(' ', '', $user_name);

$blResponse = $objUserFactory->createUser($user_name, $_POST['password'], $_POST['firstname'], $_POST['lastname'], $_POST['youremail']);

if($blResponse === FALSE) {
    echo "Unable to save";
}