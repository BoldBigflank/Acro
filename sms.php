<?php
// Set up the database
$con = mysql_connect("localhost","root","");
mysql_select_db("acro", $con);

// Set up Twilio
// Only incoming messages, doesn't require credentials

// Set up Pubnub
include_once('Pubnub.php');
$pubnub = new Pubnub( 'publish key', 'subscription key' );


// Get the useful parameters
$Body = $_GET['Body'];
$From = $_GET['From'];

// If body is a number
if(is_numeric($Body)){
	// increment the database
	mysql_query("UPDATE acro SET votes = votes + 1 WHERE id='$Body'");
	
	// Get the acronym
	$res = mysql_query("SELECT acronym FROM acro WHERE id='$Body'");
	$acronymRow = mysql_fetch_assoc($res);
	$acronym = $acronymRow['acronym'];
}
// else it's a string
else{
	// Make the acronym
	$acronym = "";
	foreach (explode(" ", $Body) as $word)
		$acronym .= strtolower($word[0]);
	
	// add body to the database as an entry
	mysql_query("INSERT INTO acro SET acronym='$acronym', body='$Body', acro.from='$From'");
}

// Update the pubnub channel for the acronym
$res = mysql_query("SELECT * FROM acro WHERE acronym='$acronym'");
$phrases = "";
while($phrase = mysql_fetch_assoc($res)){
	$phrases .= "<li>$phrase[id] - $phrase[body] ($phrase[votes])</li>";
}
$pubnub->publish(array(
    'channel' => "$acronym",
    'message' => array( 'phrases' => $phrases )
));


// Respond to the user
echo "<Response><Sms>You did it!</Sms></Response>";

?>