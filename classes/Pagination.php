<?php
class Pagination {
    public $total;
    public $limit;

    public function __construct($limit) {
        $this->limit = $limit;
    }

    public function has_pagination() {
        if (($this->total / $this->limit) > 1) {
            return true;
        } else {
            return false;
        }
    }

    public function get_total_pages() {
        return ceil($this->total / $this->limit);
    }

    public function current_page() {
        if (isset($_GET['page'])) {
            return $_GET['page'];
        } else {
            return 1;
        }
    }

    public function get_offset() {
        if (isset($_GET['page'])) {
            $offset = $this->limit * ($this->current_page() - 1);
            return $offset;
        }  
    }

    public function has_next() {
        if ($this->current_page() < $this->get_total_pages()) {
            return true;
        } else {
            return false;
        }
    }

    public function has_prev() {
        if ($this->current_page() > 1) {
            return true;
        } else {
            return false;
        }
    }

    public function first() {
        return 1;
    }

    public function last() {
        return $this->get_total_pages();
    }

    public function jump_to($page, $location) {
        if ($page === "") {
            $errors[] = '<i class="error">Please enter a number</i>';
            echo output_errors($errors);
            return false;
        } else {
            $page = (int)$page;
            if (is_int($page)) {
                if ($page <= 0) {
                    $errors[] = '<i class="error">Your number cannot be 0 or a negative number</i>';
                    echo output_errors($errors);
                    return false;
                } else {
                    if ($page > $this->get_total_pages()) {
                        $errors[] = '<i class="error">This page does not exist yet</i>';
                        echo output_errors($errors);
                        return false;
                    } else {
                        Redirect::to($location);
                        return true;
                    }
                }
            } else {
                $errors[] = '<i class="error">Please enter a whole number</i>';
                echo output_errors($errors);
                return false;
            }
        }
    }
}