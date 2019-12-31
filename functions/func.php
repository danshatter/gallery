<?php
function Autoloader($class_name) {
    if (strpos($_SERVER['PHP_SELF'], 'admin') > 3) {
        require_once '../classes/'.$class_name.'.php';
    } else {
        require_once 'classes/'.$class_name.'.php';
    }
}

function escape($string) {
    return htmlentities($string, ENT_QUOTES, 'UTF-8');
}

function output_errors($errors) {
    return implode('<br/>', $errors);
}

function admin_protect() {
    if (!isset($_SESSION['id'])) {
        Redirect::to('login.php');
    }
}

function login_redirect($page) {
    if (isset($_SESSION['id'])) {
        Redirect::to($page);
    }
}

function size_convert($size) {
    if ($size < 1048576) {
        return round(($size/1024), 2).' KB';
    } else {
        return round(($size/1048576), 2).' MB';
    }
}

function time_convert($time) {
    $stamp = strtotime($time);
    $timecheck = time() / 86400;
    $timecheck_int = floor($timecheck);
    $check = $stamp / 86400;
    $check_int = floor($check);    
    if ($check_int === $timecheck_int) {
        return 'Today at '.date('h:i a', $stamp);
    } elseif ($check_int === $timecheck_int - 1) {
        return 'Yesterday at '.date('h:i a', $stamp);
    } else {
        return date('l d F, Y', $stamp);
    }
}

function upload_errors($error) {
    if ($error === 0) {
        return 'No error';
    } elseif ($error === 1) {
        return 'is larger than upload_max_filesize';
    } elseif ($error === 2) {
        return 'is larger than MAX_FILE_SIZE';
    } elseif ($error === 3) {
        return 'failed because of partial upload';
    } elseif ($error === 4) {
        return 'No file selected';
    } elseif ($error === 6) {
        return 'failed because of no temporary directory';
    } elseif ($error === 7) {
        return 'failed because file can\'t write to disk';
    } else {
        return 'failed because of an error with the file extension';
    }
}