<?php
//live versie op http://www.baswijma.nl/cd/index.php
$host = "db.baswijma.nl";
$user = "md63588db461608";
$pass = "********";
$name = "md63588db461608";

if(isset($_POST["add"])) {
	$error = "";
	if($_POST["artist"] == "") { $error .= "<li>Enter artist name</li>"; }
	if(preg_match('/[\'^£%*()}{@#~><>,|=+¬]/', $_POST["artist"])) { $error .= "<li>Can't use illegal characters in artist name</li>"; }
	if($_POST["album"] == "") { $error .= "<li>Enter album name</li>"; }
	if(preg_match('/[\'^£%*()}{@#~><>,|=+¬]/', $_POST["album"])) { $error .= "<li>Can't use illegal characters in album name</li>"; }
	if($_POST["day"] == "" || $_POST["month"] == "" || $_POST["year"] == "") { $error .= "<li>Enter release date</li>"; }
	if($_POST["genre"] == "") { $error .= "<li>Select genre</li>"; }
	if($_POST["minutes"] == 0 && $_POST["seconds"] == 0) { $error .= "<li>Enter length of album</li>"; }
	if($_POST["number_of_songs"] == 0) { $error .= "<li>Select number of songs</li>"; }
	if($error != "") { $error = "<ul>".$error."</ul>"; }
	if($error == "") {

		$conn = new mysqli($host,$user,$pass,$name);
		if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); } 
		
		$released = mktime(12,0,0,$_POST["month"],$_POST["day"],$_POST["year"]);
		$length = ($_POST["minutes"]*60)+$_POST["seconds"];
		
		$sql = "INSERT INTO collection (artist,album,released,genre,length,number_of_songs)
		VALUES ('".$_POST["artist"]."',
				'".$_POST["album"]."',
				'".$released."',
				'".$_POST["genre"]."',
				'".$length."',
				'".$_POST["number_of_songs"]."')";

		if ($conn->query($sql) === TRUE) {
			$red = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']."?add=yes";
			header("Location: ".$red);
		} else {
		    $error = "Error: " . $sql . "<br>" . $conn->error;
		}

$conn->close();
	}
}
?>

<html>
<head>
<title>CD Collection</title>
<link href="style.css" rel="stylesheet" type="text/css" />
</head>

<body>

<h1>My Album Collection</h1>
<?php
if($_GET["add"] = "yes") { echo "<h4>Album added to collection</h4>"; }

$mysqli = new mysqli($host,$user,$pass,$name);

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

$sql = "SELECT * FROM collection";
if(isset($_GET["o"])) {
	switch($_GET["o"]) {
		case 1: $order = "artist"; break;
		case 2: $order = "album"; break;
		case 3: $order = "released"; break;
		case 4: $order = "genre"; break;
		case 5: $order = "length"; break;
		case 6: $order = "number_of_songs"; break;
		default: "id"; break;	
	}
	$sql .= " ORDER BY ".$order;
}
$result = $mysqli->query($sql);
$data = mysqli_fetch_all ($result, MYSQLI_ASSOC);


echo "<table cellpadding='0' cellspacing='0'>
		<tr>
			<th><a href='?o=1'>Artist</a></th>
			<th><a href='?o=2'>Album</a></th>
			<th><a href='?o=3'>Release date</a></th>
			<th><a href='?o=4'>Genre</a></th>
			<th><a href='?o=5'>Length</a></th>
			<th><a href='?o=6'># of songs</a></th>
		</tr>";


$s = 0;
foreach($data as $cd => $info) {
	
	if($s == 0) { $bg = "#FFF"; $s = 1; } else { $bg = "#EEE"; $s = 0; }
	
	$length = floor($info['length']/60).":".($info['length']-floor($info['length']/60)*60);
	echo "<tr class='row' style='background-color:".$bg."'>
			<td>".$info['artist']."</td>
			<td>".$info['album']."</td>
			<td>".date("j F Y",$info['released'])."</td>
			<td>".$info['genre']."</td>
			<td>".$length."</td>
			<td>".$info['number_of_songs']."</td>
		</tr>";
	
}

echo "</table>";



/* free result set */
$result->free();

/* close connection */
$mysqli->close();



?>

<h2>Add CD</h2>
<form action="" method="post">
	<input type="text" name="artist" maxlength="50" placeholder="Enter artist name" /><br />
	<input type="text" name="album" maxlength="100" placeholder="Enter album name" /><br />
    <select name="genre">
    	<option value="" width="300px">Select genre</option>
        <option value="Pop">Pop</option>
        <option value="Rock">Rock</option>
        <option value="Metal">Metal</option>
        <option value="Punk">Punk</option>
        <option value="Grunge">Grunge</option>
        <option value="Hiphop">Hiphop</option>
        <option value="Alternative Rock">Alternative Rock</option>
        <option value="Postpunk Revival">Postpunk Revival</option>
        <option value="Blackened Deathmetal">Blackened Deathmetal</option>
    </select><br />
    Release Date:
    <select name="day">
    	<option value="">Day</option>
    	<?php
			$c = 1;
			while($c <= 31) { echo "<option value='".$c."'>".$c."</option>"; $c++; }
		?>
    </select>
    <select name="month">
    	<option value="">Month</option>
        <option value="1">January</option>
        <option value="2">February</option>
        <option value="3">March</option>
        <option value="4">April</option>
        <option value="5">May</option>
        <option value="6">June</option>
        <option value="7">July</option>
        <option value="8">August</option>
        <option value="9">September</option>
        <option value="10">October</option>
        <option value="11">November</option>
        <option value="12">December</option>
    </select>
    <select name="year">
    	<option value="">Year</option>
    	<?php
			$c = 1970;
			while($c <= date("Y")) { echo "<option value='".$c."'>".$c."</option>"; $c++; }
		?>
    </select><br />
    Album length:
    <select name="minutes">
    	<option value="0">0</option>
    	<?php
			$c = 1;
			while($c <= 150) { echo "<option value='".$c."'>".$c."</option>"; $c++; }
		?>
    </select> minutes, 
    <select name="seconds">
    	<option value="0">0</option>
    	<?php
			$c = 1;
			while($c <= 59) { echo "<option value='".$c."'>".$c."</option>"; $c++; }
		?>
    </select> seconds<br />
    Number of songs: 
    <select name="number_of_songs">
    	<option value="0">0</option>
    	<?php
			$c = 1;
			while($c <= 50) { echo "<option value='".$c."'>".$c."</option>"; $c++; }
		?>
        
    </select><br />
    <input type="submit" name="add" value="Add to collection" />
        
    
</form>
<?php if($error != "") { echo $error; } ?>
</body>
</html>
