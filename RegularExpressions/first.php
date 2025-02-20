<?php

    function numX2($matches) {
        return "'" . $matches[1] * 2 . "'";
    }

    $string = "sdf23'5'4'4dfgd3'67'd";
    $pattern = "/'(\d*)'/";

    $result = preg_replace_callback($pattern, "numX2", $string);

    echo($result);
