<?php
class Image {
    private static $_instance;

    public static function instance() {
        if (!isset(self::$_instance)){
            return self::$_instance = new Image;
        }
        return self::$_instance;
    }

    public function upload($file, $img_name, $caption, $visible) {
        $GLOBALS['errors'];
        if (trim($img_name) === "" || trim($caption) === "") {
            $errors[] = '<i class="error">Fill in all fields</i>';
            echo output_errors($errors);
            return false;
        } else {
            if (!isset($visible)) {
                $errors[] = '<i class="error">Fill in all fields</i>';
                echo output_errors($errors);
                return false;
            } else {
                $file_name = $file['name'];
                $file_size = $file['size'];
                $file_tmp_name = $file['tmp_name'];
                $file_error = $file['error'];               
                $file_type = $file['type'];
                $allowed = array('jpg', 'jpeg', 'png');

                for ($i=0; $i < count($file_name); $i++) { 
                    if ($file_error[$i] !== 0) {
                        $errors[] = '<i class="error">'.$file_name[$i].' '.upload_errors($file_error[$i]).'</i>';
                    }
                }

                if (count($errors) !== 0) {
                    echo output_errors($errors);
                    return false;
                } else {
                    for ($i=0; $i < count($file_name); $i++) {
                        $ext = explode('.', $file_name[$i]);
                        $ext = strtolower(end($ext));
                        if (!in_array($ext, $allowed)) {
                            $errors[] = '<i class="error">'.$file_name[$i].' should be jpg, jpeg or png</i>';
                        }
                    }

                    if (count($errors) !== 0) {
                        echo output_errors($errors);
                        return false;
                    } else {
                        if (count($file_name) > 18) {
                            $errors[] = '<i class="error">You cannot upload more than 18 pictures at a time</i>';
                            echo output_errors($errors);
                            return false;
                        } else {
                            if (array_sum($file_size) > 8000000) {
                                $errors[] = '<i class="error">Your total uploads must not exceed 8MB</i>';
                            echo output_errors($errors);
                            return false;
                            } else {
                                if (file_exists('../images/'.$img_name.'.'.$ext)) {
                                    $errors[] = '<i class="error">This file already exists</i>';
                            echo output_errors($errors);
                            return false;
                                } else {
                                    if (!preg_match("/^[a-zA-Z\\s0-9]*$/", $img_name)) {
                                    $errors[] = '<i class="error">Your filename must consist of only alphabets and numbers';
                                    echo output_errors($errors);
                                    return false;
                                    } else {
                                        $success = array();
                                        for ($i=0; $i < count($file_name); $i++) {
                                            if ($i === 0) {
                                                if (!move_uploaded_file($file_tmp_name[$i], '../images/'.$img_name.'.'.$ext)) {
                                                    $errors[] = '<i class="error">image '.$file_name[$i].' failed to upload</i>';
                                                } else {
                                                    if (DB::instance()->insert('images', array('img_name', 'caption', 'type', 'size', 'visible'), array(escape($img_name.'.'.$ext), escape($caption), escape($file_type[$i]), escape(size_convert($file_size[$i])), escape($visible)))) {
                                                        $success[] = '<i class="success">image '.$file_name[$i].' uploaded successfully</i>';
                                                    } else {
                                                        $errors[] = '<i class="error">An internal error occurred while uploading '.$file_name[$i].'</i>';
                                                    }
                                                }
                                            } else {
                                                if (!move_uploaded_file($file_tmp_name[$i], '../images/'.$img_name.'('.($i + 1).')' .'.'.$ext)) {
                                                    $errors[] = '<i class="error">image '.$file_name[$i].' failed to upload</i>';
                                                } else {
                                                    if (DB::instance()->insert('images', array('img_name', 'caption', 'type', 'size', 'visible'), array(escape($img_name.'('.($i + 1).')'.'.'.$ext), escape($caption), escape($file_type[$i]), escape(size_convert($file_size[$i])), escape($visible)))) {
                                                        $success[] = '<i class="success">image '.$file_name[$i].' uploaded successfully</i>';
                                                    } else {
                                                        $errors[] = '<i class="error">An internal error occurred while uploading '.$file_name[$i].'</i>';
                                                    }
                                                }
                                            }
                                        }

                                        if (count($errors) !== 0) {
                                            echo output_errors($success);
                                            echo '<br/><br/>';
                                            echo output_errors($errors);
                                            return false;
                                        } else {
                                            return true;
                                        } 
                                    }
                                }
                            }
                        }
                    }
                }                         
            }
        }
    }

    public function download($name, $type) {
        $path = 'images/'.$name;
        $size = filesize($path);
        if (file_exists($path)) {
            header('Content-Type: '.$type);
            header('Content-Disposition: attachment; filename="'.$name.'"');
            header('Pragma: public');
            header('Cache-Control: must-revalidate, pre-check=0, post-check=0');
            header('Accept-Ranges: bytes');
            header('Content-Length: '.$size);
            ob_end_clean();
            readfile($path);
            die();
        } else {
            die('<h1>You cannot download this file as it does not exist or has been deleted</h1>');
        }
    }

}