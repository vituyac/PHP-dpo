<?php

    function solve($data) {
        $lines = explode("\n", trim($data));
        $output = "";
        foreach ($lines as $line) {
            $line = str_replace("::", ":qqqq:",$line);
            $ips = explode(":", trim($line));
            $str = "";
            foreach ($ips as $num) {
                if(strlen($num) == 4) {
                    $str .= $num . ":";
                } elseif(strlen($num) == 1) {
                    $str .= "000" . $num . ":";
                } elseif(strlen($num) == 2) {
                    $str .= "00" . $num . ":";
                } elseif(strlen($num) == 3) {
                    $str .= "0" . $num . ":";
                } elseif(strlen($num) == 0) {
                    $str .= "0000" . ":";
                }
            }
            $str = substr($str, 0, -1);
            $str = str_replace("qqqq:", str_repeat("0000:", (7 - substr_count($str, ":") + 1)), $str);
            $output .= $str . "\n";
        }
        return substr($output, 0, -1);
    }
?>