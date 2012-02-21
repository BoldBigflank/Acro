<?php
// Connect to the database
$con = mysql_connect("localhost","root","");
mysql_select_db("acro", $con);

// Load the initial data from the database
$acronym = isset($_GET['acronym']) ? strtolower($_GET['acronym']) : "TEST";

// If there is no acronym, create one


// Get phrases for that acronym
$res = mysql_query("SELECT * FROM acro WHERE acronym = '$acronym'");

// Prepare the html for the list item
$phrases = "";
while($phrase = mysql_fetch_assoc($res)){
	$phrases .= "<li>$phrase[id] - $phrase[body] ($phrase[votes])</li>";
}

?><html>
<head>
	<title>Acro</title>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
</head>
<body>
	<div id="acronym"><?php echo $acronym; ?></div>
	<ul id="phrases"><?php echo $phrases; ?></ul>
	
	<button onClick="history.go()">New phrase</button>
	
<div pub-key="public key" sub-key="subscription key" ssl="off" origin="pubsub.pubnub.com" id="pubnub"></div>
<script src="http://cdn.pubnub.com/pubnub-3.1.min.js"></script>
<script>(function(){
    // LISTEN FOR MESSAGES
    PUBNUB.subscribe({
    channel  : "<?php echo $acronym; ?>",
    callback : function(message) { 
		$("#phrases").html(message.phrases)
	}
})
 
})();</script>
</body>
</html>