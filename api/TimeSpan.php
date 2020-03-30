<?php


/**
 * Class TimeSpan
 */
class TimeSpan {

    /**
     * @var $start DateTime
     * @var $end DateTime
     * @var $is_span bool by default a span is true, if it is null it is set to false
     */
    public $start;
    public $end;
    public $is_span;

    function __construct(){
        $this -> is_span = true;
    }
    /**
     * is_valid() checks two conditions:
     * 1. the $start & $end to be of type DateTime
     * 2. $start should be less than $end
     * @return bool
     */
    public function is_valid(){
        if (!$this -> is_span) return false;
        if (!is_a($this->start,"DateTime") || !is_a($this->end,"DateTime") )  return false;
        if ( $this -> start >= $this -> end) return false;
        if ( $this -> start < $this -> end) return true;
    }

    /**
     * it receives a TimeSpan as $span and checks if it is a subset of $this
     * @param $span TimeSpan
     * @return bool
     */
    public function is_subset_of($span){
        if ( $this -> start < $span -> start ) return false;
        if ( $this -> end > $span -> end ) return false;
        return true;
    }

    /**
     * It receives a TimeSpan as $span and if it has an intersection it returns the intersection
     * It actually tries to find the spans' overlaps and return it
     * if they have not any intersection it returns a TimeSpan with $is_span false
     * @param $span TimeSpan
     * @return $this|TimeSpan
     */
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

    /**
     * as a help function, it prints out the Timespan
     */
    public function print_span(){
        //it is a helper function
        echo '[ ' ;
        echo $this -> start -> format('Y-m-d H:i');  echo ' - ';
        echo $this -> end -> format('Y-m-d H:i');               echo ' ]<br>';
    }

    /**
     * @param $datetime DateTime it is a given DateTime
     * @param $added_time string the added time string for changing the $datetime
     * @return DateTime
     */
    public function add_time($datetime, $added_time){
        $datetime->add( date_interval_create_from_date_string($added_time));
        return $datetime;
    }
}