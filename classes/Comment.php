<?php
class Comment {
    private static $_instance;

    public static function instance() {
        if (!isset(self::$_instance)){
            return self::$_instance = new Comment;
        }
        return self::$_instance;
    }

    public function post_comment($id, $name, $comment) {
        $GLOBALS['errors'];
        if ($name === "" || $comment === "") {
            $errors[] = '<i class="error">To post a comment, name and comment fields are required</i>';
            echo output_errors($errors);
            return false;
        } else {
            if (!preg_match("/^[a-zA-Z\\s0-9]*$/", $name)) {
                $errors[] = '<i class="error">Your name can contain spaces but must only consist of alphabets and numbers</i>';
                echo output_errors($errors);
                return false;
            } else {
                if (DB::instance()->insert('comments', array('img_id', 'name', 'comment'), array($id, $name, nl2br($comment)))) {
                    return true;
                } else {
                    $errors[] = '<i class="error">An error occured. Please try again later</i>';
                    echo output_errors($errors);
                    return false;
                }
            }
        }
    }

    public function count($id) {
        $GLOBALS['errors'];
        $result = DB::instance()->select_by_sql("SELECT COUNT(*) as `count` FROM `comments` WHERE `img_id` = ?", array($id));
        $count = array_shift($result);
        return $count->count;
    }
}