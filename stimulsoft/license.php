<?php

// You can check the user authorization to send a license key only if the result is positive.

// For security reasons, we recommend to deny access to the 'license.key' file or change its name.

if (file_exists("license.key")) {
	$license = file_get_contents("license.key");
	echo $license;
}

?>