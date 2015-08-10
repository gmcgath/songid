<div class="menubar">
<p>&nbsp;</p>
<p class="menubaruser">
<?php

require_once('bin/loggersetup.php');

$usr = $_SESSION['user'];
if ($usr) {
	echo ($usr->name);
}
else {
	$GLOBALS["logger"]->info ("No user???");
}
?>
</p>
<ul class="menubar">
	<li><a href="cliplist.php">Clips</a></li>
	<li><a href="reports.php">Reports</a></li> 
<?php
	if ($usr->hasRole(User::ROLE_SUPERUSER)) {
?>
	<li><a href="admin.php">User administration</a></li>
<?php
	}
?>
	<li><a href="logout.php">Log out</a></li>
	<li><a href="help.html">Help</a></li>
</ul>
</div>
<div style="clear:both"><p>&nbsp;</p></div>
