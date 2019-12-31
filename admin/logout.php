<?php
require_once '../core/init.php';
if (isset($_SESSION['id'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['logout'])) {
            if (User::instance()->logout()) {
                Redirect::to(SITE_ROOT.'/admin/login.php');
            } else {
                die('<h1>An error occurred while logging you out. Please try again later</h1>');
            }
        }
    }
} else {
    Redirect::to(SITE_ROOT.'/index.php');
}