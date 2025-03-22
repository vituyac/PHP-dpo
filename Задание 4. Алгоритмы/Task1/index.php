<?php
    function solve($data) {

        $arrayDates = [];
        $arrayCounts = [];

        $strings = explode("\n", trim($data));
        foreach ($strings as $line) {
            $parts = preg_split('/ {2,}/', trim($line));
            if (isset($arrayDates[$parts[0]])) {
                $arrayCounts[$parts[0]] += 1;
                $date1 = DateTime::createFromFormat('d.m.Y H:i:s', $arrayDates[$parts[0]]);
                $date2 = DateTime::createFromFormat('d.m.Y H:i:s', $parts[1]);
                if ($date2 > $date1) {
                    $arrayDates[$parts[0]] = $parts[1];
                }
            }
            else {
                $arrayCounts[$parts[0]] = 1;
                $arrayDates[$parts[0]] = $parts[1];
            }  
        }

        $result = "";

        foreach ($arrayCounts as $ad => $value) {
            $result .= "$arrayCounts[$ad] $ad $arrayDates[$ad]\n";
        }
        
        return(rtrim($result, "\n"));
    }
?>