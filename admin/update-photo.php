<?php
require_once '../core/init.php';
admin_protect();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id']) && isset($_POST['update'])) {
        $id = $_POST['id'];
        $picture = DB::instance()->select_by_sql("SELECT * FROM `images` WHERE `id` = ?", array($id));
        if (count($picture) === 0) {
            die('<h1>This picture does not exist or has already been deleted</h1>');
        } else {
            $pic = array_shift($picture);
            if ($pic->visible === 1) {
                $visible = 0;
            } else {
                $visible = 1;
            }
            if (DB::instance()->update('images', array('visible'), array($visible), 'id', $id)) {
                $_SESSION['update'] = 'success';
                if (isset($_SERVER['HTTP_REFERER'])) {
                    Redirect::to($_SERVER['HTTP_REFERER']);
                } else {
                    Redirect::to(SITE_ROOT.'/admin/list-photos.php');
                }
            } else {
                die('<h1>An internal error occurred. Please try again later</h1>');
            }
        }
    } else {
        Redirect::to(SITE_ROOT.'/index.php');
    }
}