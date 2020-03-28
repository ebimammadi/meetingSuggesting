<?php


class TimeSpan {
    public $start; //start
    public $end; //end
    public $is_span;// it's a flag, will be set to false for NULL TimeSpan

    function __construct(){
        $this -> is_span = true;
    }

    public function is_valid(){
        if (!$this -> is_span) return false;
        if (!is_a($this->start,"DateTime") || !is_a($this->end,"DateTime") )  return false;
        if ( $this -> start >= $this -> end) return false;
        if ( $this -> start < $this -> end) return true;
        $this -> is_span = false;
        return false;
    }
    public function is_equal($span){
        if ( !$this -> is_valid() ) return false;
        if (($this -> start == $span -> start) && ($this -> end == $span -> end) ) return true;
        return false;
    }
    public function is_subset_of($span){
        if ( $this -> start < $span -> start ) return false;
        if ( $this -> end > $span -> end ) return false;
        return true;
    }
    public function intersection_with($span){

        if (!$this -> is_valid()) return $this;
        if (!$span -> is_valid()) return $span;

        $spanA = clone $this;
        $spanB = clone $span;
        if ( $spanA -> is_subset_of($spanB) ) return $spanA;
        if ( $spanB -> is_subset_of($spanA) ) return $spanB;

        if ( ($spanB -> start <= $spanA -> start) && ($spanB -> end <= $spanA -> end) ) {
            if ($spanB -> end == $spanA -> start) {
                $spanA -> is_span = false;
                return $spanA;
            }
            if ($spanB -> end > $spanA -> start) {
                $spanA->end = $spanB->end;
                return $spanA;
            }
        }

        if ( ($spanA -> start <= $spanB -> start) && ($spanA -> end <= $spanB -> end) ) {
            if ($spanA -> end == $spanB -> start) {
                $spanA -> is_span = false;
                return $spanA;
            }
            if ($spanA -> end > $spanB -> start) {
                $spanB->end = $spanA->end;
                return $spanB;
            }
        }

        $spanA -> is_span = false;
        return $spanA;
    }
    public function print_span(){
        echo '[ ' ;
        echo $this -> start -> format('Y-m-d H:i');  echo ' - ';
        echo $this -> end -> format('Y-m-d H:i');               echo ' ]<br>';
    }
    public function add_time($datetime, $added_time){
        $datetime->add( date_interval_create_from_date_string($added_time));
        return $datetime;
    }
}