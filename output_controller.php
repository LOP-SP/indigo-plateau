/*
 Retrieves all information from DB in a JSON for JavaScript manipulation, in
 decreasing order of total points.

 {
	name: name,
	total_points: total_points,
	stuff: [ [event1, reason1, time1], [event2, reason2, time2], ...]
 }
*/

// Template tag: Returns the ranking HTML table.
function indigo_plateau_ranking () {
	$ranking = '';
	
	return $ranking;
}

$ranking = [];

json_encode($ranking);