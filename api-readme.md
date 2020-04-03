# API  
  
This JSON API can be found in the folder [api](api).  
An online copy is available at this address:  
`tadbir.net/tests/meetings/api/`  
  
This API accepts 5 requests: 

 - **`get-office-hours`**
 
	* sample: `tadbir.net/tests/meetings/api/?request=get-office-hours`  
	* it receives two string numbers `start` and `end`  for the office hours
	
 - **`set-office-hours`**
	 * sample `tadbir.net/tests/meetings/api/?request=set-office-hours&start=8&end=17` 
	 * `start` and `end` are sent to the api, it accepts valid hours and start should be less than end
	 * in case of success it receieves `"message_type" : "success"` otherwise  `"message_type" : "error"` with a `"message"`
	 
 - **`get-employees`**
	 * sample `tadbir.net/tests/meetings/api/?request=get-employees` 
	 * it receives an array of objects like in the following:
	  `[
	 { "id": "103222943108469712161093620402295866178",
"name": "Irma Taylor"
}, ...]`
	* otherwise it receives an empty array `[]` or an error  `"message_type" : "error"` with a `"message"` 
	
 - **`get-current-meetings`** 
	 * it receives `ids[]` which is the id of employees 
	 * `start` and `end` which should be in the format of `YYYY-mm-dd HH:ii:ss` start and end are the earliest and latest time
	 * `timezone` is send but currently is not applied in this version!
	 * sample `tadbir.net/tests/meetings/api/?request=get-current-meetings&ids[]=156281747655501356358519480949344976171&ids[]=57646786307395936680161735716561753784&start=2015-03-12 13:00:00&end=2015-03-14 12:00:00&timezone=UTC`
	 * it receives an array of objects like in the following:
	 * `[{ "id": "57646786307395936680161735716561753784",
"meetings": [
{
"start": {
"date": "2015-03-13 08:00:00.000000",
"timezone_type": 3,
"timezone": "UTC"
},
"end": {
"date": "2015-03-13 13:00:00.000000",
"timezone_type": 3,
"timezone": "UTC"
}
}
]
},...]` Please see the online api
	* it seperates each employee's meeting by different objects
 - **`get-suggestion-meetings`**
	 *   it receives  `ids[]`  which is the id of employees it can be single or multiple.
	*   `start`  and  `end`  which should be in the format of  `YYYY-mm-dd HH:ii:ss`  start and end are the earliest and latest time
	*   `timezone`  is send but currently is not applied in this version!
	* `meeting_length` is a number now accepted between `0` and `300`
	* a sample is like `tadbir.net/tests/meetings/api/?request=get-suggestion-meetings&ids[]=57646786307395936680161735716561753784&meeting_length=200&start=2015-03-12 13:00:00&end=2015-03-14 12:00:00&timezone=UTC`
	*	the result is an array of suggesting spans like `{ "start": {
"date": "2015-03-12 13:00:00.000000",
"timezone_type": 3,
"timezone": "UTC"
},
"end": {
"date": "2015-03-12 16:20:00.000000",
"timezone_type": 3,
"timezone": "UTC"
}
},`

 ## Other notes 
* Online version is available at tadbir.net/tests/meetings/api/
* Timezone is required to send but it is not applied in this version
* it can recieve several employees at the same time
* On the online version current data of the employees are available between  **2015-01-01** and **2015-03-31**

* for more info about this api refer to [README.md](README.md)