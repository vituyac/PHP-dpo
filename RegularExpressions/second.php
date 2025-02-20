<?php

    function replace_link($matches) {
        return "http://sozd.parlament.gov.ru/bill/" . $matches[1];
    }

    $string = file_get_contents('index_input.txt');
    $pattern = "/http:\/\/asozd\.duma\.gov\.ru\/main\.nsf\/\(Spravka\)\?OpenAgent&RN=(\d*-\d*)&\d*/";

    $result = preg_replace_callback($pattern, "replace_link", $string);

    file_put_contents('index_output.txt', $result);

