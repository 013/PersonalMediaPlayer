<?php
/*
id
title
type
imdbid
path
date
*/
define("DB_DSN", "mysql:host=localhost;dbname=files");
define("DB_USERNAME", "username");
define("DB_PASSWORD", "password");
$MV_LOC = "/media/c2fb2794-b436-4446-bf3a-f8b05596f8d4/Movies/";
$TV_LOC = "/media/c2fb2794-b436-4446-bf3a-f8b05596f8d4/TV/";

$action = isset($_GET['a']) ? $_GET['a'] : '';
if ($action == 'insert' && isset($_GET['title']) && isset($_GET['type']) && isset($_GET['id']) && isset($_GET['path']) && isset($_GET['date']) ) {
	insert($_GET['title'], $_GET['type'], $_GET['id'], $_GET['path'], $_GET['date']);
	die();
}
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Movies and Shit</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.2/css/bootstrap.min.css">
<style>
.switch {position: relative; width: 85px; -webkit-user-select:none; -moz-user-select:none; -ms-user-select: none;}
.switch-checkbox {display: none;}
.switch-label {display: block; overflow: hidden; cursor: pointer; border: 1px solid #cccccc; border-radius: 0px;}
.switch-inner {width: 200%; margin-left: -100%;-moz-transition: margin 0.3s ease-in 0s; -webkit-transition: margin 0.3s ease-in 0s;-o-transition: margin 0.3s ease-in 0s; transition: margin 0.3s ease-in 0s;}
.switch-inner:before, .switch-inner:after {float: left; width: 50%; height: 35px; padding: 0; line-height: 31px;font-size: 14px; color: white; font-family: Trebuchet, Arial, sans-serif; font-weight: bold;-moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box;border: 2px solid transparent;background-color: #EEEEEE; color: #333333;text-align: center;}
.switch-inner:before {content: "Search";}
.switch-inner:after {content: "Remote";}
.switch-checkbox:checked + .switch-label .switch-inner {margin-left: 0;}
.switch-checkbox:checked + .switch-label .switch-switch {right: 0px;}
.play-button {
background:#eee;
text-decoration:none;
color:#333;
font-family:Arial, sans-serif;
font-size:11px;
font-weight:bold;
padding:3px 5px;
border:1px solid #aaa;
border-radius:3px;
cursor:default;
}
.play-button:hover {
background-color:#f2f2f2;
border-color:#888;
box-shadow:0 0 2px #ccc;
}
.play-button:active {
vertical-align:-1px;
}
</style>
<script src="//code.jquery.com/jquery-1.10.2.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$("#switch").click(function() { $(".search-form").toggle(); $(".remote-form").toggle(); });
});
</script>
</head>
<body>
	<div class="container">
		<div class="header">
			<h3 class="text-muted">Movies <span class="glyphicon glyphicon-film"></span></h3>
		</div>
		<div class="jumbotron">
			<div class="switch">
				<input type="checkbox" name="" class="switch-checkbox" id="switch" value="remote" <?=(isset($_GET['type'])?"checked":"")?>>
				<label class="switch-label" for="switch">
					<div class="switch-inner"></div>
					<div class="switch-switch"></div>
				</label>
			</div>
			
			<form role="form" class="search-form">
				<div class="form-group">
					<label for="exampleInputEmail1">Search</label>
					<input type="text" name="s" class="form-control" id="movietitle" placeholder="Movie Title" value="<?=(isset($_GET['s'])?$_GET['s']:"")?>">
					<input type="hidden" name="a" value="search">
				</div>
				<button type="submit" class="btn btn-lg btn-primary">Search</button>
			</form>
	
			<div class="remote-form" style="display: none">
				<button type="button" class="btn btn-success btn-lg">
					<span class="glyphicon glyphicon-play"></span>
				</button>
				<button type="button" class="btn btn-danger btn-lg">
					<span class="glyphicon glyphicon-stop"></span>
				</button>
				<button type="button" class="btn btn-default btn-lg">
					<span class="glyphicon glyphicon-resize-full"></span>
				</button>
			</div>
		</div>

		<? 
		// Once this reaches 3, close the div and open a new one
		$AMrow = 0;
		?>
		<div class="row">

<?php

switch($action) {
	case 'search':
		search($_GET['s']);
		break;
	case 'info':
		info($_GET['id']);
		break;
	case 'play':
		$path = $MV_LOC.getPath($_GET['id']);
		$serverIP = getServerIP();
		echo "ssh -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no -i /home/ryan/text.1234 ryan@$serverIP \"DISPLAY=:0 nohup /usr/bin/vlc --fullscreen --no-sub-autodetect-file \"$path\"\"";
		system("ssh -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no -i /home/ryan/text.1234 ryan@$serverIP \"DISPLAY=:0 nohup /usr/bin/vlc --fullscreen --no-sub-autodetect-file \"$path\"\"");
		break;
	default:
		homepage();
		break;
}

?>
	
</div>

      <div class="footer">

        <p><!--&copy; 2013-->Linnit</p>
      </div>

    </div> <!-- /container -->

<!-- Latest compiled and minified JavaScript -->
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.2/js/bootstrap.min.js"></script>
</body>
</html>
<?

function homepage() {
	// Get latest added films
	$conn = new PDO (DB_DSN, DB_USERNAME, DB_PASSWORD);
	$sql = "SELECT * FROM files LIMIT 0, 15";
	$st = $conn->prepare($sql);
	$st->execute();
	
	while ($row = $st->fetch()) {
		$obj = getInfo($row['IMDbID']);
		echo objHTML($obj["title"], $obj["year"], $obj["imdbRating"], $obj["released"], $obj["genre"], $obj["plot"], $obj["posterurl"], $row['id']);
	}
	
	$conn = null;
}

function search($term) {
	// Search for a searchterm
	$conn = new PDO (DB_DSN, DB_USERNAME, DB_PASSWORD);
	$sql = "SELECT * FROM files WHERE title like :searchterm";
	$st = $conn->prepare($sql);
	$st->bindValue(":searchterm", '%'.$term.'%', PDO::PARAM_STR);
	$st->execute();
	while ($row = $st->fetch()) {
		$obj = getInfo($row['IMDbID']);
		echo objHTML($obj["title"], $obj["year"], $obj["imdbRating"], $obj["released"], $obj["genre"], $obj["plot"], $obj["posterurl"], $row['id']);
	}
	$conn = null;
}

function getInfo($id) {
	// Search local mdb
	$conn = new PDO (DB_DSN, DB_USERNAME, DB_PASSWORD);
	$sql = "SELECT * FROM imdb WHERE imdbid = :id";
	$st = $conn->prepare($sql);
	$st->bindValue(":id", $id, PDO::PARAM_STR);
	$st->execute();
	$row = $st->fetch();
	if (count($row) == 1) {
		// If film not found, find it
		$json = file_get_contents("http://www.omdbapi.com/?i=".$id);
		$obj = json_decode($json, true);
		insertmdb($id, $obj["Title"], $obj["Year"], $obj["imdbRating"], $obj["Released"], $obj["Genre"], $obj["Plot"], $obj["Poster"]);
	
		$sql = "SELECT * FROM imdb WHERE imdbid = :id";
		$st = $conn->prepare($sql);
		$st->bindValue(":id", $id, PDO::PARAM_STR);
		$st->execute();
		$row = $st->fetch();
	}	
	$conn = null;
	//echo "<br>";
	//echo count($row);
	return $row;
}

function insert($title, $type, $id, $path, $date) {
		$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
		$sql = "INSERT INTO files ( title, type, IMDbID, path, date) VALUES ( :title, :type, :id, :path, :date)";
		$st = $conn->prepare($sql);
		$st->bindValue(":title", $title, PDO::PARAM_STR);
		$st->bindValue(":type", $type, PDO::PARAM_STR);
		$st->bindValue(":id", $id, PDO::PARAM_STR);
		$st->bindValue(":path", $path, PDO::PARAM_STR);
		$st->bindValue(":date", $date, PDO::PARAM_STR);
		$st->execute();
		$id = $conn->lastInsertId();
		echo $id;
		$conn = null;
}

function insertmdb($id, $title, $year, $imdbRating, $released, $genre, $plot, $poster) {
	// If imdbid is not found in imdb, add it
	$conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
	$sql = "INSERT INTO imdb ( imdbid, title, year, imdbRating, released, genre, plot, posterurl) VALUES ( :id, :title, :year, :imdbRating, :released, :genre, :plot, :posterurl)";
	$st = $conn->prepare($sql);
	$st->bindValue(":id", $id, PDO::PARAM_STR);
	$st->bindValue(":title", $title, PDO::PARAM_STR);
	$st->bindValue(":year", $year, PDO::PARAM_STR);
	$st->bindValue(":imdbRating", $imdbRating, PDO::PARAM_STR);
	$st->bindValue(":released", $released, PDO::PARAM_STR);
	$st->bindValue(":genre", $genre, PDO::PARAM_STR);
	$st->bindValue(":plot", $plot, PDO::PARAM_STR);
	$st->bindValue(":posterurl", $poster, PDO::PARAM_STR);
	$st->execute();
	$id = $conn->lastInsertId();
	$conn = null;
}

function getPath($id) {
	// Get path of a file
	$conn = new PDO (DB_DSN, DB_USERNAME, DB_PASSWORD);
	$sql = "SELECT path FROM files WHERE id = :id";
	$st = $conn->prepare($sql);
	$st->bindValue(":id", $id, PDO::PARAM_INT);
	$st->execute();
	$row = $st->fetch();
	$conn = null;
	$path =  str_replace(' ', '\\\\\\ ', $row['path']);
	$path = str_replace('(', '\\\\\\(', $path);
	$path = str_replace(')', '\\\\\\)', $path);
	return $path;
}

function getServerIP() {
	return '151.224.105.183';
}

function objHTML($title, $year, $rating, $released, $genre, $plot, $posterurl, $id) {
	global $AMrow;
	if ($posterurl == 'N/A') {
		$posterurl = 'http://linnit.pw/na.png';
	}
	$AMrow += 1;
	if ($AMrow == 3) { $x = "</div><div class=\"row\">"; } else { $x=''; }
	
	return <<<HTML
<div class="col-md-4">	
<h4>$title <a class="play-button" href="?a=play&id=$id"><span class="glyphicon glyphicon-play"></span></a></h4>
<p><div style="width: 140px; float: left; padding-right: 5px;">
<img src="http://linnit.pw/image.php?url=$posterurl" alt="$title" class="img-thumbnail img-responsive"></div>
$plot
</p>
</div>
$x
HTML;
}

?>
