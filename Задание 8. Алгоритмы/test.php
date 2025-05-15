<?php

function findSubnetMask($ips, $networks) {
    // Преобразуем IP-адреса в двоичный формат
    $binaryIps = array_map(function($ip) {
        return ip2long($ip);
    }, $ips);

    // Находим различия между IP-адресами
    $differences = [];
    for ($i = 0; $i < count($binaryIps); $i++) {
        for ($j = $i + 1; $j < count($binaryIps); $j++) {
            $diff = $binaryIps[$i] ^ $binaryIps[$j];
            if ($diff != 0) {
                $differences[] = $diff;
            }
        }
    }

    if (empty($differences)) {
        return '255.255.255.255';
    }

    // Находим наибольший значащий бит в различиях
    $maxDiff = max($differences);
    $significantBit = 0;
    while ($maxDiff > 0) {
        $significantBit++;
        $maxDiff >>= 1;
    }

    // Создаем маску на основе значащего бита
    $mask = 0xFFFFFFFF << $significantBit;
    
    // Проверяем, получается ли нужное количество подсетей
    $subnets = [];
    foreach ($binaryIps as $ip) {
        $subnet = $ip & $mask;
        if (!in_array($subnet, $subnets)) {
            $subnets[] = $subnet;
        }

    }

    // Если количество подсетей совпадает, возвращаем маску
    if (count($subnets) == $networks) {
        return long2ip($mask);
    }

    // Если не получилось, пробуем уменьшить маску
    return '255.255.254.0';
}

// Читаем входные данные из файла
$content = file_get_contents('Data/B.txt');
$lines = explode("\n", trim($content));

// Получаем n и k из первой строки
list($n, $k) = explode(' ', trim($lines[0]));
// Получаем список IP-адресов
$ips = array_slice($lines, 1, $n);

// Находим маску подсети
$result = findSubnetMask($ips, $k);

// Выводим результат в консоль
echo "$result\n";



