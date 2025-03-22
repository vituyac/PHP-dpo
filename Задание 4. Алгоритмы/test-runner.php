<?php
    include $argv[1];

    $directory = $argv[2];

    $test_number = 1;

    while (file_exists("$directory/{$test_number}in.txt")) {

        $input_data = file_get_contents("$directory/{$test_number}in.txt");

        $expected_output = trim(file_get_contents("$directory/{$test_number}out.txt"));
        $ans = trim(str_replace("\r\n", "\n", $expected_output));

        $result = solve($input_data);

        if ($result === $ans) {
            echo "Test $test_number: OK\n";
        } else {
            echo "Test $test_number: FAIL\n";
            echo "Expected:\n$ans\n";
            echo "Got:\n$result\n";
        }

        $test_number++;
    }