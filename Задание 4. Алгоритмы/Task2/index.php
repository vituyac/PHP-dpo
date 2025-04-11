<?php

    // Основная функция, принимающая входные данные в виде строки
    function solve($data) {

        $categories = []; // Массив для хранения категорий
        $strings = explode("\n", trim($data)); // Разбиваем входную строку на строки (по строкам)
        $result = ""; // Строка для финального результата

        // Обрабатываем каждую строку
        foreach ($strings as $line) { 
            $parts = explode(" ", trim($line)); // Разбиваем строку по пробелам
            
            // Добавляем категорию в массив
            $categories[] = [
                'id' => $parts[0],
                'title' => $parts[1],
                'left_node' => $parts[2],
                'right_node' => $parts[3]
            ];
        }
        
        // Сортируем категории по полю 'left_node' (сначала идут родительские узлы)
        usort($categories, function($a, $b) {
            return $a['left_node'] <=> $b['left_node']; // сравнение значений 'left_node'
        });
        
        $stack = []; // Стек для отслеживания текущего уровня вложенности
        
        // Перебираем отсортированные категории
        foreach ($categories as $category) {
            // Если текущий элемент выходит за границы последнего узла в стеке, удаляем элементы из стека
            while (!empty($stack) && end($stack)['right_node'] < $category['right_node']) {
                array_pop($stack);
            }
            
            // Уровень вложенности = количество элементов в стеке
            $level = count($stack);

            // Добавляем отступы "-" в зависимости от уровня вложенности
            $result .= str_repeat("-", $level) . $category['title'] . "\n";
            
            // Добавляем текущую категорию в стек
            $stack[] = $category;
        }

        return(rtrim($result, "\n")); // Удаляем последний символ переноса строки и возвращаем результат
    }

?>
