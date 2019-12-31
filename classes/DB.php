<?php
class DB {
    private $_pdo;
    private static $_instance;

    public function __construct() {
        try {
            $this->_pdo = new PDO(DB_TYPE.':'.'host='.DB_HOST.';dbname='.DB_NAME, DB_USERNAME, DB_PASSWORD);
            $this->_pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            $this->_pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    public function __destruct() {
        $this->_pdo = null;
    }

    public static function instance() {
        if (!isset(self::$_instance)){
            return self::$_instance = new DB;
        }
        return self::$_instance;
    }

    public function insert($table, $fields = array(), $values = array()) {
        $GLOBALS['errors'];
        if (count($fields) === count($values)) {
            $sql_field = '`'.implode('`, `', $fields).'`';
            $placeholder_array = array_fill(0, count($fields), '?');
            $placeholder = implode(', ', $placeholder_array);
            $sql = "INSERT INTO `$table` ($sql_field) VALUES ($placeholder);";
            $stmt = $this->_pdo->prepare($sql);
            for ($i=0; $i < count($values); $i++) {
                $j = $i + 1;
                $stmt->bindValue($j, $values[$i]);
            }
            if ($stmt->execute()) {
                return true;
            } else {
                $errors[] = '<i class="error">Values and Fields not equal</i>';
                echo output_errors($errors);
                return false;
            }
        } else {
            $errors[] = '<i class="error">Values and Fields not equal</i>';
            echo output_errors($errors);
            return false;
        }      
    }

    public function delete($table, $field, $value) {
        $GLOBALS['errors'];
        $sql = "DELETE FROM `$table` WHERE `$field` = ?;";
        $stmt = $this->_pdo->prepare($sql);
        $stmt->bindValue(1, $value);
        if ($stmt->execute()) {
            return true;
        } else {
            $errors[] = '<i class="error">Values and Fields not equal</i>';
            echo output_errors($errors);
            return false;
        }     
    }

    public function update($table, $fields = array(), $values = array(), $locator_field, $locator_value) {
        $GLOBALS['errors'];
        if (count($fields) === count($values)) {
            $placeholder_array = array_fill(0, count($fields), '?');
            $query_array = array();
            for ($i=0; $i < count($values); $i++) { 
                $query_array[] = "`$fields[$i]` = $placeholder_array[$i]"; 
            }
            $query_string = implode(', ', $query_array);
            $sql = "UPDATE `$table` SET {$query_string} WHERE `{$locator_field}` = ?;";
            $stmt = $this->_pdo->prepare($sql);
            for ($i=0; $i < count($values); $i++) { 
                $j = $i + 1;
                $stmt->bindValue($j, $values[$i]);
            }
            $stmt->bindValue(count($values) + 1, $locator_value);
            if ($stmt->execute()) {
                return true;
            } else {
                $errors[] = '<i class="error">An internal error occurred</i>';
                echo output_errors($errors);
                return false;
            }
        } else {
            $errors[] = '<i class="error">Values and Fields not equal</i>';
            echo output_errors($errors);
            return false;
        }  
    }

    public function select_by_sql($sql, $values = array()) {
        $GLOBALS['errors'];
        $count = substr_count($sql, '?');
        if ($count === count($values)) {
            $stmt = $this->_pdo->prepare($sql);
            if (count($values) >= 1) {
                $j = 1;
                foreach ($values as $value) {
                    $stmt->bindValue($j, $value);
                    $j++;
                }
            }
            if ($stmt->execute()) {
                $result = $stmt->fetchAll();
                return $result;
            } else {
                $errors[] = '<i class="error">An internal error occurred</i>';
                echo output_errors($errors);
                return false;
            }
        } else {
            $errors[] = '<i class="error">Values and Fields not equal</i>';
            echo output_errors($errors);
            return false;
        }   
    }

}



    