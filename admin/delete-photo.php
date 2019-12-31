<?php
require_once '../core/init.php';
admin_protect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete']) && isset($_POST['id'])) {
        $id = $_POST['id'];
        $picture = DB::instance()->select_by_sql("SELECT * FROM `images` WHERE `id` = ?", array($id));
        if (count($picture) === 0) {
            die('<h1>This picture does not exist or has already been deleted</h1>');
        } else {
            $pic = array_shift($picture);
            if (DB::instance()->delete('images', 'id', $id) && unlink('../images/'.$pic->img_name) && DB::instance()->delete('comments', 'img_id', $id)) {
                $_SESSION['delete'] = 'success';
                Redirect::to(SITE_ROOT.'/admin/list-photos.php');
            } else {
                die('<h1>An internal error occurred</h1>');
            }
        }
    } else {
        Redirect::to(SITE_ROOT.'/index.php');
    }
}