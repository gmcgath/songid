<?php

/* Include this on every page that requires a logged-in user */


if (!$_SESSION['user']) {
	header ("Location: login.php", true, 302);
	return;
}
?>