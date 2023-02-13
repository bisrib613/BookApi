<?php
function e7061($e){
	$ed = base64_decode($e);
	$n = openssl_decrypt("$ed","AES-256-CBC","nembelasangka123",0,"9801928471645872");
	return $n;
}
?>
