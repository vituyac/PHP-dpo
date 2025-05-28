<?php
// Подключаем файл с решением (первый аргумент командной строки)
include $argv[1];

// Папка с тестовыми файлами (второй аргумент командной строки)
$directory = $argv[2];

// Номер текущего теста, начинаем с 1
$test_number = 1;

// Пока существуют файлы с входными данными для теста
while (file_exists("$directory/{$test_number}in.txt")) {

    // Считываем входные данные из файла
    $input_data = file_get_contents("$directory/{$test_number}in.txt");

    // Считываем ожидаемый вывод и убираем лишние пробелы и символы переноса строк
    $expected_output = trim(file_get_contents("$directory/{$test_number}out.txt"));
    $ans = trim(str_replace("\r\n", "\n", $expected_output)); // нормализуем переносы строк

    // Вызываем функцию solve с входными данными
    $result = solve($input_data);

    // Особая проверка для решения из "Task3/index.php"
    if ($argv[1] == "Task3/index.php") {
        $test_ok = true;

        // Разбиваем результат и ответ на строки
        $result = explode("\n", $result);
        $ans = explode("\n", $ans);

        // Проверяем поэлементно ключ и значение с допуском по числам
        for ($i = 0; $i < count($result); $i++) {
            [$key1, $val1] = explode(" ", $result[$i]);
            [$key2, $val2] = explode(" ", $ans[$i]);

            // Если ключи не совпадают или разница в числах больше 0.0013 — тест провален
            if ($key1 !== $key2 || abs(floatval($val1) - floatval($val2)) >= 0.0013) {
                echo "Test $test_number: FAIL\n";
                $test_ok = false;
            }
        }
        // Если все проверки пройдены — тест успешен
        if ($test_ok) {
            echo "Test $test_number: OK\n";
        }
    } 
    // Для остальных решений проверяем точное совпадение вывода
    elseif ($result === $ans) {
        echo "Test $test_number: OK\n";
    } else {
        // Если вывод не совпадает — показываем ошибку и ожидаемый/полученный результат
        echo "Test $test_number: FAIL\n";
        echo "Expected:\n$ans\n";
        echo "Got:\n$result\n";
    }

    // Переходим к следующему тесту
    $test_number++;
}
