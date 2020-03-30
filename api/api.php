<?php
include_once "Meeting.php";


/**
 * @param $request
 * it checks if the request is one of the possible requests
 * there are currently this 5 GET requests
 */
function validate_request_methods($request){
    $all_requests = array(
        "get-employees",
        "get-current-meetings",
        "get-suggestion-meetings",
        "get-office-hours",
        "set-office-hours"
    );
    if (!in_array($request,$all_requests))
        exit( json_encode(
            array(
                    "message_type" => "error",
                    "message"=>"invalid request"
                )
            ));
}

function generate_request_response($request){
    switch ($request){
        case "get-employees": exit( json_encode( get_employees()) );
        case "get-office-hours": exit( json_encode( get_office_hours()) );
        case "set-office-hours": exit( json_encode( set_office_hours($_REQUEST)) );
        case "get-current-meetings": exit ( json_encode( get_current_meetings($_REQUEST)) );
        case "get-suggestion-meetings": exit ( json_encode( get_suggestion_meetings($_REQUEST)) );
    }

}

function get_employees(){
    if (count($_REQUEST) != 1 )
        return array(
            "message_type" => "error",
            "message" => "invalid request",
        );

    $meeting = new Meeting();

    $employees = $meeting -> get_employees_from_file();
    return $employees;
}

function get_office_hours(){
    if (count($_REQUEST) != 1 )
        return array(
            "message_type" => "error",
            "message" => "invalid request",
        );
    $string = file('../data/settings.txt');
    $hours = explode(";", $string[0]);
    return array(
        "start" => $hours[0],
        "end" => $hours[1]
    );
}

function set_office_hours($params){
    if (count($_REQUEST) != 3 )
        return array(
            "message_type" => "error",
            "message" => "invalid request"
        );
    if (!is_numeric($params["start"]) || !is_numeric($params["end"]))
        return array(
            "message_type" => "error",
            "message" => "invalid request"
        );
    $start = (int)$params["start"];
    $end = (int)$params["end"];
    if ( ($start <= 0)||($end <= 0)||($start >= $end))
        return array(
            "message_type" => "error",
            "message" => "invalid start/end param"
        );
    if ( ($start>22) || ($end>23))
        return array(
            "message_type" => "error",
            "message" => "invalid start/end param"
        );
    $file = fopen('../data/settings.txt',"w");
    fwrite($file,$start.';'.$end);
    fclose($file);
    return array(
        "message_type" => "success",
        "message" => "Saved"
    );
}

function get_current_meetings($params){
    if (count($params)!= 5)
        return array(
            "message_type" => "error",
            "message" => "invalid request",
        );
    if (!isset($params["ids"])){
        return array(
            "message_type" => "error",
            "message" => "invalid request",
        );
    }
    $ids = $params["ids"];
    if ( (count($ids)<1) ||(count($ids)>10) )
        return array(
            "message_type" => "error",
            "message" => "upto 10 employees is valid",
        );
    if (!isset($params["start"]) || !isset($params["end"])){
        return array(
            "message_type" => "error",
            "message" => "invalid request",
        );
    }
    if (!validateDate(trim($params["start"]), 'Y-m-d H:i:s')) {
        return array(
            "message_type" => "error",
            "message" => "invalid datetime format for start (Y-m-d H:i:s)",
        );
    }
    if (!validateDate(trim($params["end"]), 'Y-m-d H:i:s')) {
        return array(
            "message_type" => "error",
            "message" => "invalid datetime format for end (Y-m-d H:i:s)",
        );
    }

    $meeting = new Meeting();

    $start = new DateTime( trim($params["start"]), $meeting -> time_zone );
    $end = new DateTime( trim($params["end"]), $meeting -> time_zone );
    $main_span = new TimeSpan();
    $main_span -> start = $start;
    $main_span -> end = $end;
    if (!$main_span -> is_valid()){
        return array(
            "message_type" => "error",
            "message" => "The time span is not valid!",
        );
    }
    return $meeting -> get_current_meetings_from_file($ids, $main_span);
}

function get_suggestion_meetings($params) {
    if (count($params)!= 6)
        return array(
            "message_type" => "error",
            "message" => "invalid request",
        );
    if (!isset($params["ids"])){
        return array(
            "message_type" => "error",
            "message" => "invalid request",
        );
    }
    $ids = $params["ids"];
    if ( (count($ids)<1) ||(count($ids)>10) )
        return array(
            "message_type" => "error",
            "message" => "upto 10 employees is valid",
        );

    if (!isset($params["meeting_length"]) || !is_numeric($params["meeting_length"]))
        return array(
            "message_type" => "error",
            "message" => "Meeting length is NOT valid.",
        );
    $meeting_length = (int)$params["meeting_length"];
    if ( ($meeting_length <= 0)||($meeting_length > 300))
        return array(
            "message_type" => "error",
            "message" => "Meeting length should be between 0 and 300 minutes"
        );

    if (!isset($params["start"]) || !isset($params["end"])){
        return array(
            "message_type" => "error",
            "message" => "invalid request",
        );
    }
    if (!validateDate(trim($params["start"]), 'Y-m-d H:i:s')) {
        return array(
            "message_type" => "error",
            "message" => "invalid datetime format for start (Y-m-d H:i:s)",
        );
    }
    if (!validateDate(trim($params["end"]), 'Y-m-d H:i:s')) {
        return array(
            "message_type" => "error",
            "message" => "invalid datetime format for end (Y-m-d H:i:s)",
        );
    }

    $meeting = new Meeting();

    $start = new DateTime( trim($params["start"]), $meeting -> time_zone );
    $end = new DateTime( trim($params["end"]), $meeting -> time_zone );
    $main_span = new TimeSpan();
    $main_span -> start = $start;
    $main_span -> end = $end;
    if (!$main_span -> is_valid()){
        return array(
            "message_type" => "error",
            "message" => "The time span is not valid!",
        );
    }
    return $meeting -> get_suggesting_meetings($ids, $main_span, $meeting_length);

}

function validateDate($date, $format = 'Y-m-d H:i:s')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) == $date;
}