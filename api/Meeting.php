<?php
include_once "TimeSpan.php";

/**
 * Class Meeting
 * This class extends the TimeSpan class and adds meetings and employees features
 */
class Meeting extends TimeSpan {

    public $file = '../data/freebusy.txt'; //data file
    public $setting_file = '../data/settings.txt'; //saves the start&end office hours
    public $default_start_string = "2015-01-01 00:00:00";
    public $default_end_string = "2016-01-01 00:00:00";

    public $time_zone;

    function __construct(){
        $this -> time_zone = new DateTimeZone("UTC");
    }

    /**
     * @param $a_day DateTime is a day to find its start and end office hours as DateTime
     * @return TimeSpan returns the TimeSpan its start is the start office time in DateTime format, its end is the end office Time in DateTime format
     */
    private function office_hours($a_day ){
        $string = file($this -> setting_file);
        $hours = explode(";", $string[0]);

        $start_hour = (int)$hours[0];
        $end_hour = (int)$hours[1];

        $span = new TimeSpan();
        $start = clone $a_day ;
        $start -> setTime( $start_hour ,0 ,0 );
        $span  -> start = $start;
        $end = clone $a_day;
        $end -> setTime( $end_hour, 0, 0 );
        $span -> end = $end;
        return $span;
    }

    /**
     * @param $start DateTime it rounds the start time to 8, 8:30, 9, 9:30 and so on
     * @return DateTime the rounded start DateTime
     */
    private function round_start_hour($start ){
        //if the start minute is 00 or 30 it should be rounded to the top near value;
        $minute = intval( $start -> format("i") );
        if ($minute%30 !== 0) {
            $add_minute = 30 - ($minute%30);
            $start -> add( date_interval_create_from_date_string($add_minute.' minutes') );
        }
        return $start;
    }

    /**
     * @param $employees_id array this is the array of employees to query in freebusy file,
     *                            it can consist of single or multiple employees
     * @param $main_span DateTime is the span to query for (the meetings earliest and latest date and time)
     *                            if it is NULL it uses the default given in the class
     * @return array returns an array of objects, each element is an object for each employee_id, it includes its current meetings
     * @throws Exception
     */
    public function get_current_meetings_from_file($employees_id, $main_span = NULL){
        $file = $this -> file;
        if (!file_exists($file)) {
            error_log('The file does not exist! ',$file);
            return array();
        }
        if ($main_span === NULL ) {
            $main_span = new TimeSpan();
            $main_span -> start = new DateTime ( $this -> default_start_string );
            $main_span -> end = new DateTime( $this -> default_end_string );
        }
        $free_busy_file_rows = file($file);
        $employees_meetings = array();

        for( ; sizeof($free_busy_file_rows) > 0 ; ){
            $row_fetched = explode(";",array_shift($free_busy_file_rows));
            if (count($row_fetched) === 4){
                $a_meeting = new TimeSpan();
                $a_meeting -> start = new DateTime($row_fetched[1], $this -> time_zone);
                $a_meeting -> end = new DateTime($row_fetched[2], $this -> time_zone);
                $employee_id = $row_fetched[0];
                if ( $a_meeting -> is_subset_of($main_span) && in_array($employee_id, $employees_id))  {
                    $employees_meetings = $this -> append_employees_meetings($employees_meetings,$a_meeting,$employee_id);
                }
            }
        }
        return $employees_meetings;
    }

    /**
     * @param $employees_id array       this is the array of employees to query in freebusy file,
     *                                  it can consist of single or multiple employees
     * @param $limited_span DateTime    is the span to query for (the meetings earliest and latest date and time)
     * @param $meeting_length Integer   is the length of the meeting
     * @return array returns the array of suggesting spans
     * @throws Exception
     */
    public function get_suggesting_meetings($employees_id, $limited_span, $meeting_length ){
        $possible_meetings = $this -> get_possible_meetings($limited_span,$meeting_length);
        $current_employees_meetings = $this -> get_current_meetings_from_file($employees_id, $limited_span);
        $current_meetings = array ();

        if ( count($current_employees_meetings) === 0 ) {
            return $possible_meetings;
        }
        foreach($current_employees_meetings as $array){
            $current_meetings = array_merge( $current_meetings , $array -> meetings  );
        }

        $suggesting_meetings = array();

        for ($i = 0; $i < count($possible_meetings) ; $i++){
            $meeting = new TimeSpan();
            $meeting = $possible_meetings[$i];
            $flag = true;
            foreach ($current_meetings as $current_meeting){
                $intersection_span = new TimeSpan();
                $intersection_span = $meeting -> intersection_with($current_meeting);
                if ($intersection_span -> is_valid()) {
                    $flag = false;
                    break;
                }
            }
            if ($flag == true) array_push($suggesting_meetings, $meeting);
        }

        return $suggesting_meetings;
    }


    /**
     * this function is for adding current meetings for each employee's meeting
     * @param $employees_meetings array    is an array of employees with its current meetings
     * @param $meeting TimeSpan            it is the meeting to be added
     * @param $id Integer                  it is the id of the employee
     * @return array
     */
    private function append_employees_meetings($employees_meetings, $meeting, $id){
        $employee = new stdClass();
        $employee -> id = $id;
        $employee -> meetings = array();
        $id = $this -> find_id_in_employees_meetings_array($employees_meetings, $employee->id);
        if ( $id < 0 ) {
            array_push($employees_meetings, $employee);
            $id = count($employees_meetings) - 1;
        }
        array_push($employees_meetings[$id]->meetings, $meeting);
        return $employees_meetings;
    }
    private function find_id_in_employees_meetings_array($current_meetings, $id){
        if (count($current_meetings) === 0) return -1;
        foreach ($current_meetings as $key => $employee)
            if ($id == $employee->id) return $key;
        return -1; //the id is not in the preparing array
    }


    /**
     * @param $main_span TimeSpan     is the span for generating raw possible meetings
     * @param $meeting_length Integer is the length of proposed meetings
     * @return array returns an array of possible meetings
     */
    function get_possible_meetings($main_span, $meeting_length){
        $meetings = array();
        for ( $i = 1, $day_start = $main_span->start; ; $i++) {
            $workingSpan = $this->office_hours($day_start);
            $day_start = clone $workingSpan->start;
            $intersection = new stdClass(); //#############
            //$intersection = $this->get_intersection($workingSpan, $main_span);
            $intersection = $workingSpan->intersection_with( $main_span);
            if ($intersection-> is_valid() ){
                $pairs = $this->generate_pairs($intersection, $meeting_length);
                $meetings = array_merge($meetings, $pairs);
            }
            if ((!$intersection -> is_valid() ) && ($i !== 1)) { //in case the intersection is null return the possible meetings
                return $meetings;
            }
            $day_start->add(date_interval_create_from_date_string('1 day'));
        }
    }


    private function generate_pairs($limited_span, $meeting_length){
        $limited_span -> start = $this -> round_start_hour( $limited_span -> start);
        $span_pairs = array();
        for ( $start = $limited_span->start ; ; ){
            $end = clone $start;
            $end = $this -> add_time($end,$meeting_length.' minutes');
            //checks if the $end have exceeded the $span->end; then it exits the loop and returns
            if ($end > $limited_span -> end) {
                return $span_pairs;
            }
            $pair = new TimeSpan();
            $pair -> start = $start;
            $pair -> end = $end;
            array_push($span_pairs, $pair);
            $nextStart = clone $start;
            //next possibility can start 30 minutes later!
            $start = $this -> add_time($nextStart, '30 minutes');
        }
    }

    /**
     * @param  $file string is the filepath of freebusy.txt
     * @return array it extracts and returns an array of employees including name and id
     */
    public function get_employees_from_file($file = NULL){
        if ($file === NULL) $file = $this->file;
        $file_rows = file($file);
        $employees = array();
        for($i=0; sizeof($file_rows) > 0;$i++){
            $row_array_fetched = explode(";" ,array_shift($file_rows));
            if( ( count($row_array_fetched) === 2) && (trim($row_array_fetched[1]) != "") ) {
                $employee = new stdClass();
                $employee -> id = trim($row_array_fetched[0]);
                $employee -> name = trim($row_array_fetched[1]);
                array_push($employees,$employee);
            }
        }
        return $employees;
    }

}
