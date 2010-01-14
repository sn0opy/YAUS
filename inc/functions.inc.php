<?php

function error($msg){
	echo '<div id="error">'.$msg.'</div>';
}

function base36($hash, $decode = true) {
	if($decode) {
		return base_convert($hash, 10, 36);
	} else {
		return base_convert($hash, 36, 10);
	}
}

?>
