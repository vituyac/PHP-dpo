<?php
    include $argv[1];
    $directory = $argv[2];
    $Tasks = glob($directory . '*.dat');
    $Answers = glob($directory . '*.ans');
    
    for ($i = 0; $i < count($Tasks); $i++) {
        $data = file_get_contents($Tasks[$i]);
        
        $result = solve($data);

        echo "{$Tasks[$i]}:";

        $ans = file_get_contents($Answers[$i]);
        $ans = trim(str_replace("\r\n", "\n", file_get_contents($Answers[$i])));
        
        if($ans == $result) {
            echo("OK\n");
        } else {
            echo("ERROR\n");
        }
    }



