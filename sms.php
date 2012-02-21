<?php
// Set up the database
include_once("constants.php");

// Set up Twilio
// Only incoming messages, doesn't require credentials

// Set up Pubnub
include_once('Pubnub.php');
$pubnub = new Pubnub( $pn_pubkey, $pn_subkey );


// Get the useful parameters
$Body = $_GET['Body'];
$From = $_GET['From'];

// If body is a number
if(is_numeric($Body)){
	$action = "vote";
	// increment the database
	mysql_query("UPDATE acro SET votes = votes + 1 WHERE id='$Body'");
	
	// Get the acronym
	$res = mysql_query("SELECT acronym FROM acro WHERE id='$Body'");
	$acronymRow = mysql_fetch_assoc($res);
	$acronym = $acronymRow['acronym'];
}
// else it's a string
else{
	$action = "entry";
	// Make the acronym
	$acronym = "";
	foreach (explode(" ", $Body) as $word)
		$acronym .= strtoupper($word[0]);
	
	// add body to the database as an entry
	mysql_query("INSERT INTO acro SET acronym='$acronym', body='$Body', acro.from='$From'");
}

// Update the pubnub channel for the acronym
$res = mysql_query("SELECT * FROM acro WHERE acronym='$acronym'");
$phrases = "";
while($phrase = mysql_fetch_assoc($res)){
	$phrases .= "<tr><td>$phrase[id]</td><td>$phrase[body]</td><td>$phrase[votes]</td></tr>";
}
$pubnub->publish(array(
    'channel' => "$acronym",
    'message' => array( 'phrases' => $phrases )
));


// Respond to the user
echo "<Response><Sms>Your $action for the acronym $acronym has been received.</Sms></Response>";

?>