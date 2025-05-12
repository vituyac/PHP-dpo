<?php

    function normalize_xml($xmlStr) {
        $xmlStr = trim($xmlStr);
        if ($xmlStr === '') return '';  // Возвращаем пустую строку без ошибки

        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;
        $doc->loadXML($xmlStr);
        return trim($doc->saveXML());
    }

    // Подключаем файл с функцией solve($data1, $data2)
    include $argv[1];

    // Путь к директории с тестами
    $directory = rtrim($argv[2], '/') . '/';

    // Получаем список всех файлов вида *_products.xml
    $productsFiles = glob($directory . '*_products.xml');

    // Сортировка по имени файла (чтобы 001, 002 шли правильно)
    sort($productsFiles);

    // Проходим по всем найденным product-файлам
    foreach ($productsFiles as $productFile) {
        // Определим "ключ" теста, например 001
        $testId = basename($productFile, '_products.xml');

        // Составляем имена соответствующих файлов
        $sectionsFile = $directory . $testId . '_sections.xml';
        $resultFile = $directory . $testId . '_result.xml';

        // Проверяем, что все три файла существуют
        if (!file_exists($sectionsFile) || !file_exists($resultFile)) {
            echo "$testId: MISSING FILE(S)\n";
            continue;
        }

        // Загружаем содержимое файлов
        $data1 = file_get_contents($productFile);
        $data2 = file_get_contents($sectionsFile);
        $expected = normalize_xml(file_get_contents($resultFile));
        $actual = normalize_xml(solve($data1, $data2));

        // Сравнение результата
        // echo $actual;
        // echo $expected;
        if ($actual != $expected) {
            echo "мой ответ: $actual";
            echo "\n\n\n";
            echo "какой должен быть: $expected";
        }
        echo "$testId: " . ($actual === $expected ? "OK" : "FAIL") . "\n";
    }
?>