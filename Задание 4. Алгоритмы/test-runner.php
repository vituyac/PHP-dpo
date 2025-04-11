<?php
    include $argv[1];

    $directory = $argv[2];

    $test_number = 1;

    while (file_exists("$directory/{$test_number}in.txt")) {

        $input_data = file_get_contents("$directory/{$test_number}in.txt");

        $expected_output = trim(file_get_contents("$directory/{$test_number}out.txt"));
        $ans = trim(str_replace("\r\n", "\n", $expected_output));

        $result = solve($input_data);

        if ($argv[1] == "Task3/index.php") {
            $test_ok = true;
            $result = explode("\n", $result);
            $ans = explode("\n", $ans);
            for ($i = 0; $i < count($result); $i++) {
                [$key1, $val1] = explode(" ", $result[$i]);
                [$key2, $val2] = explode(" ", $ans[$i]);

                if ($key1 !== $key2 || abs(floatval($val1) - floatval($val2)) >= 0.0013) {
                    echo "Test $test_number: FAIL\n";
                    $test_ok = false;
                }
            }
            if ($test_ok) {
                echo "Test $test_number: OK\n";
            }
        } elseif ($result === $ans) {
            echo "Test $test_number: OK\n";
        } else {
            echo "Test $test_number: FAIL\n";
            echo "Expected:\n$ans\n";
            echo "Got:\n$result\n";
        }

        $test_number++;
    }