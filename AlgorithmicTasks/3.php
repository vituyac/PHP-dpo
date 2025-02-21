<?php
    function solve($data) {
        $lines = explode("\n", trim($data));
        $output = "";
        foreach ($lines as $line) {
            $pattern = '/<(.*)>\s(.*)/';
            preg_match($pattern, $line, $matches);
            $str = $matches[1];
            $params = explode(" ", trim($matches[2]));
            if($params[0] == "S") {
                if ($params[1] <= strlen($str) && strlen($str) <= $params[2]) {
                    $output .= "OK\n";
                } else {
                    $output .= "FAIL\n";
                }
            } elseif($params[0] == "N") {
                if ($params[1] <= $str && $str <= $params[2] && (int)$str == $str) {
                    $output .= "OK\n";
                } else {
                    $output .= "FAIL\n";
                }
            } elseif($params[0] == "P") {
                $num_pattern = "/^\+7\s\(\d{3}\)\s\d{3}\-\d{2}-\d{2}$/";
                if (preg_match($num_pattern, $str)) {
                    $output .= "OK\n";
                } else {
                    $output .= "FAIL\n";
                }
            } elseif($params[0] == "D") {
                $date_pattern = "/(\d{1,2})\.(\d{1,2})\.(\d{4})\s(\d{1,2}):(\d{2})/";
                if (preg_match($date_pattern, $str, $date_match) && (checkdate($date_match[2], $date_match[1], $date_match[3]) && $date_match[4] < 24 && $date_match[5] < 60)) {
                    $output .= "OK\n";
                } else {
                    $output .= "FAIL\n";
                }
            } elseif($params[0] == "E") {
                $date_pattern = "/^(?!_)[A-Za-z0-9_]{4,30}@[A-Za-z]{2,30}\.[a-z]{2,10}$/";
                if (preg_match($date_pattern, $str)) {
                    $output .= "OK\n";
                } else {
                    $output .= "FAIL\n";
                }
            }
        }
        return substr($output, 0, -1);
    }
?>