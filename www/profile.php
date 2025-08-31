<?php
require("./connect.php");
require("base.inc.php");

// get user_id from logged-in session data
$user_id = (int) $_SESSION['user_id'];

// redirect if no user
if (!$user_id) {
  header("Location: ./");
  exit;
}

// get login sites

$userloginmap = getcolid("SELECT site, siteuserid FROM loginmap WHERE user_id = $user_id");

$useraut = getrow("SELECT users.person_id, p.firstname, p.surname FROM users INNER JOIN person p ON users.person_id = p.id WHERE users.id = $user_id");

// get name and accounts from map.

// change name

// Get friend list. Ask for friend list/feature?


$t->assign('userloginmap', $userloginmap);
$t->assign('useraut', $useraut);
$t->display('userprofile.tpl');
