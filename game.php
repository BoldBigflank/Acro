<?php
// Connect to the database
$con = mysql_connect("localhost","root","");
mysql_select_db("acro", $con);

// Load the initial data from the database
if(isset($_GET['acronym'])){
	$acronym = strtoupper($_GET['acronym']);
}
else{// If there is no acronym, create one
	// 3 - 7 characters
	$size = rand(3, 7);
	$acronym = "";
	$abc= array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z"); 
	for($i = 1; $i < $size; $i++){
		$acronym .= strtoupper($abc[rand(0,25)]);
	}
}

// Get phrases for that acronym
$res = mysql_query("SELECT * FROM acro WHERE acronym = '$acronym'");

// Prepare the html for the list item
$phrases = "";
while($phrase = mysql_fetch_assoc($res)){
	$phrases .= "<tr><td>$phrase[id]</td><td>$phrase[body]</td><td>($phrase[votes])</td></tr>";
}

?><html>
<head>
	<title>Acro</title>
	<link href='styles.css' rel='stylesheet' type='text/css'>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
</head>
<body>
	<div id="header-bar">Acro</div>
	<div id="page-wrapper">
		<div class="acronym-wrapper"><span align="center" class="acronym"><?php echo $acronym; ?></span></div>
		<div class="table wrapper">
			<table>
				<thead class="grad"><tr><th>ID</th><th>Phrase</th><th>Votes</th></tr></thead>
				<tbody id="phrases"><?php echo $phrases; ?></tbody>
			</table>
		</div>
		<button onClick="var loc = window.location.href; window.location = loc.split('?')[0];">New phrase</button>
	</div>
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