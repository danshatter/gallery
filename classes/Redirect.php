<?php
class Redirect {
    private static $_instance;

    public static function instance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new Redirect;
        }
        return self::$_instance;
    }
    
    public static function to($destination) {
        header('Location: '.$destination);
        die();
        exit();
    }
}