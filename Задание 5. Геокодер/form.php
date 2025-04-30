<?php

    // Проверяем, был ли отправлен POST-запрос
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Получаем и очищаем входные данные от пробелов
        $address = urlencode(trim($_POST['address'])); // Адрес

        // Получаем API ключ из .env файла
        $apiKey = getenv("API_KEY");

        // URL для запроса в геокодер
        $url = "https://geocode-maps.yandex.ru/v1/?apikey=$apiKey&geocode=$address&format=json&results=1&lang=ru_RU";

        $response = file_get_contents($url);
        if ($response === FALSE) {
            echo json_encode(['error' => 'Не удалось получить данные от API']);
            exit;
        }

        $data = json_decode($response, true);

        try {
            $geoObject = $data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject'];
            $formatted = $geoObject['metaDataProperty']['GeocoderMetaData']['text'];
            $coords = $geoObject['Point']['pos']; // "долгота широта"
            [$lon, $lat] = explode(' ', $coords);

            // Поиск метро
            $metroUrl = "https://geocode-maps.yandex.ru/1.x/?apikey=$apiKey&geocode=$lon,$lat&format=json&kind=metro&results=1";
            $metroResponse = file_get_contents($metroUrl);
            $metroData = json_decode($metroResponse, true);
            $metro = $metroData['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['name'] ?? 'не найдено';

            echo json_encode([
                'formatted' => $formatted,
                'coords' => "$lat, $lon",
                'metro' => $metro
            ]);
            
        } catch (Exception $e) {
            echo json_encode(['error' => 'Ошибка при обработке данных']);
        }
    }
?>
