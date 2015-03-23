<?php
class Validation {
	public static function isValidHostname($hostname) {
		//check for valid hostname as specified in rfc 1123
		//see http://stackoverflow.com/a/3824105
		$regex = "/^([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])(\.([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]{0,61}[a-zA-Z0-9]))*$/";
		return (is_string($hostname) AND strlen($hostname)<=255 AND preg_match($regex, $hostname));
	}

	public static function isValidInterfaceName($interfacename) {
		$regex = "/^[a-zA-Z0-9\-]*$/";
		return is_string($interfacename) AND strlen($interfacename)<=20 AND preg_match($regex, $interfacename);
	}
}