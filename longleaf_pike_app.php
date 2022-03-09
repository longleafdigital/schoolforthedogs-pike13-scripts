<?php

//some initial settings for heroku
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

//////////////////////////////
///BEGIN HELPER FUNCTIONS/////
//////////////////////////////

//DATETIME FUNCTIONS
//setting datetime in proper format
$date = new DateTime();
$date->sub(new DateInterval("P15D"));
$date_minus_15 = $date->format("c");

$date = new DateTime();
$date->sub(new DateInterval("P7D"));
$date_minus_7 = $date->format("c");

$date = new DateTime();
$date->add(new DateInterval("P15D"));
$date_plus_15 = $date->format("c");

$date = new DateTime();
$date->add(new DateInterval("P30D"));
$date_plus_30 = $date->format("c");

//Color from String Helper Function
function colorFromString($string) {
    $colors = [
      '#0074D9',
      '#7FDBFF',
      '#B0C4DE',
      '#32CD32',
      '#800000',
      '#9400D3',
      '#8B4513',
      '#191970',
      '#FFE4C4',
      '#DCDCDC',
      '#6495ED',
      '#BA55D3',
      '#9370DB',
      '#7B68EE',
      '#F0FFFF',
      '#0000FF',
      // this list should be as long as practical to avoid duplicates
    ];
  
    // generate a partial hash of the string (a full hash is too long for the % operator)
    $hash = substr(sha1($string), 0, 10);
  
    // determine the color index
    $colorIndex = hexdec($hash) % count($colors);
  
    return $colors[$colorIndex];
  }
////////////////////////////
///END HELPER FUNCTIONS/////
////////////////////////////

//

//swapping endpoint to use Event Occurences vs. Events
$json = file_get_contents('https://dogs.pike13.com/api/v2/front/event_occurrences?access_token=xiZyYSHbQHAlebMZPKnI4DDtpVi8kDCYkDIGm1G7&from='.$date_minus_7.'&to='.$date_plus_30);
//print_r($json);
$obj = json_decode($json);

//echo events list
$occurences = $obj->event_occurrences;
//print_r($events);

//initialize a new Array object
$occurences_array = array();
$event_ids_array = array();
$staff_members_array = array();

//attempted expansion loop
foreach($occurences as $occurence) {

    $occurence_id = $occurence->id;
    $event_id = $occurence->event_id;
    $service_id = $occurence->service_id;
    $occurence_name = $occurence->name;
    $occurence_start_time = $occurence->start_at;
    $occurence_end_time = $occurence->end_at;
    $staff_members = $occurence->staff_members; //staff array

    $event_ids_list = [
        'event_id' => $event_id
    ];

    if (str_contains($occurence_name, "Virtual Workshop:")) {
        //staff loop
        foreach ($staff_members as $staff_member) {
            
            $staff_members_list = [
                'staff_member_id' => $staff_member->id,
                'staff_member_name' => $staff_member->name
            ];

            $staff_members_array[] = $staff_members_list;
        }
    
        $occurence_details = [
            'id' => $occurence_id,
            'groupId' => $event_id,
            'title' => $occurence_name,
            'start' => $occurence_start_time,
            'end' => $occurence_end_time,
            'backgroundColor' => colorFromString($event_id),
            'staff_member' => $staff_members_list['staff_member_id'],
            'staff_member_name' => $staff_members_list['staff_member_name'],
            'description' => $occurence->description,
            'capacity_remaining' => $occurence->capacity_remaining,
            'enroll_url' => $occurence->url
        ];

        //append this to the array
        $occurences_array[] = $occurence_details;
        $event_ids_array[] = $event_ids_list;
    }
}

//transformations / formatting
$occurences_json = json_encode($occurences_array);
$staff_list = array_unique($staff_members_array, SORT_REGULAR);


/*DEBUGGING OPTIONS (uncomment to enable)*/
/*
print_r($occurences_json);
echo '<hr>';

print_r(array_unique($event_ids_array, SORT_REGULAR));
echo '<hr>';


print_r($staff_list);
echo '<hr>';

echo total;
echo $obj->total_count;
*/

?>