<?php
require_once 'core/init.php';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['download'])) {
            $id = $_POST['id'];
            $picture = DB::instance()->select_by_sql("SELECT * FROM `images` WHERE `id` = ?", array($id));
            if (count($picture) === 0) {
                die('<h1>This picture cannot be downloaded as it does not exist or has already been deleted</h1>');
            } else {
                $pic = array_shift($picture);
                if ($pic->visible === 0) {
                    die('<h1>This picture cannot be downloaded as it is on lockdown</h1>');
                } else {
                    Image::instance()->download($pic->img_name, $pic->type);
                }
            }
        }
    }