<?php

    // Читаем все данные из файла
    $data = trim(file_get_contents('Data/B.txt'));

    // Разбиваем на строки
    $lines = explode("\n", $data);

    // В первой строке — n и k
    $params = explode(" ", trim($lines[0]));
    $n = (int)$params[0];
    $k = (int)$params[1];

    // Преобразуем каждую IP-строку в 32-битное число
    $addrs = [];
    for ($i = 1; $i <= $n; $i++) {
        $ip = trim($lines[$i]);
        list($o0, $o1, $o2, $o3) = explode('.', $ip);
        // Собираем число из четырёх октетов
        $addrs[] = ($o0 << 24) | ($o1 << 16) | ($o2 << 8) | $o3;
    }

    // Ищем такую длину префикса p (0…32), чтобы число сетей == k
    for ($p = 0; $p <= 32; $p++) {
        // Строим маску: p единиц слева, затем нули
        if ($p === 0) {
            $mask = 0;
        } else {
            // -1 << (32-p) даст число с 32-(p) нулями справа и единицами слева,
            // & 0xFFFFFFFF обрезает до 32 бит
            $mask = ((-1 << (32 - $p)) & 0xFFFFFFFF);
        }

        // Собираем уникальные значения addr & mask
        $nets = [];
        foreach ($addrs as $addr) {
            $nets[$addr & $mask] = true;
        }

        if (count($nets) === $k) {
            // нашли нужную маску
            break;
        }
    }

    // Преобразуем 32-битную маску обратно в dotted-decimal
    $result = implode('.', [
        ($mask >> 24) & 0xFF,
        ($mask >> 16) & 0xFF,
        ($mask >> 8)  & 0xFF,
        $mask & 0xFF,
    ]);

    // Выводим результат
    echo $result;
