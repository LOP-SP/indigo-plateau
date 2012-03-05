/*
 Inserts player's data into DB.
*/

$name = strip_tags($_POST["name"]);
$event = strip_tags($_POST["event"]);
$reason = strip_tags($_POST["reason"]);
$time = strip_tags($_POST["time"]);

// inserts into DB

// sends confirmation to UI