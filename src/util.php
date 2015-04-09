<?php
function rstrstr($haystack,$needle) {
    // ref: http://php.net/manual/en/function.strstr.php
    return substr($haystack,strrpos($haystack, $needle)+1);
}
