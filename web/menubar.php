<div class="menubar">
<p>&nbsp;</p>
<p class="menubaruser">
<?php

/* Be sure to open $mysqli before including menubar.php */
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
<?php
	if ($usr->hasRole(User::ROLE_ADMINISTRATOR)) {
?>
	<li><a href="admin.php">User administration</a></li>
<?php
	}
?>
	<li><a href="logout.php">Log out</a></li>
</ul>
</div>
<div style="clear:both"><p>&nbsp;</p></div>
