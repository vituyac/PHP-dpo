<?php

    function normalize_xml($xmlStr) {
        $xmlStr = trim($xmlStr);
        if ($xmlStr === '') return '';

        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;
        $doc->loadXML($xmlStr);
        return trim($doc->saveXML());
    }

    include $argv[1];

    $directory = $argv[2];

    // Получаем список всех файлов вида *_products.xml
    $files = glob($directory . '*_products.xml');

    // Сортировка по имени файла (чтобы 001, 002 шли правильно)
    sort($files);

    // Проходим по всем найденным product-файлам
    foreach ($files as $file) {
        // Определим номер теста
        $test = basename($file, '_products.xml');

        // Составляем имена соответствующих файлов
        $sectionsFile = $directory . $test . '_sections.xml';
        $resultFile = $directory . $test . '_result.xml';

        // Загружаем содержимое файлов
        $data1 = file_get_contents($file);
        $data2 = file_get_contents($sectionsFile);
        $expected = normalize_xml(file_get_contents($resultFile));
        $actual = normalize_xml(solve($data1, $data2));

        echo "$test: " . ($actual === $expected ? "OK" : "FAIL") . "\n";
    }
?>