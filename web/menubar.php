<div class="menubar">
<p>&nbsp;</p>
<p class="menubaruser">
<?php
$usr = $_SESSION['user'];
if ($usr) {
	echo ($usr->name);
}
else {
	error_log ("No user???");
}
?>
</p>
<ul class="menubar">
	<li><a href="cliplist.php">Clips</a></li>
	<li><a href="reports.php">Reports</a></li> 
	<li><a href="logout.php">Log out</a></li>
</ul>
</div>
<div style="clear:both"><p>&nbsp;</p></div>
