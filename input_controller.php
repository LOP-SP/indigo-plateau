/*
 Inserts player's data into DB.
*/

function ip_insert_win ($name, $time, $event, $reason) {
	global $wpdb, $reasons;
	
	$points = $reasons($reason);
	
	$wpdb->insert(
		$wpdb->prefix . 'indigo_plateau',
		array(
			'name' => $name,
			'time' => $time,
			'event' => $event,
			'reason' => $reason,
			'points' => $points
		),
		array(
			'%s',
			'%s',
			'%s',
			'%s',
			'%d'
		)
	);
}