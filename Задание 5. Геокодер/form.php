<?php

// Проверяем, был ли отправлен POST-запрос
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Получаем и очищаем адрес из формы, кодируем для URL
    $address = urlencode(trim($_POST['address']));

    // Получаем API ключ из переменных окружения (.env)
    $apiKey = getenv("API_KEY");

    // Формируем URL для запроса к Яндекс Геокодеру (по адресу)
    $url = "https://geocode-maps.yandex.ru/v1/?apikey=$apiKey&geocode=$address&format=json&results=1&lang=ru_RU";

    // Выполняем HTTP запрос и получаем ответ
    $response = file_get_contents($url);
    if ($response === FALSE) {
        // Если не удалось получить данные — возвращаем ошибку в формате JSON
        echo json_encode(['error' => 'Не удалось получить данные от API']);
        exit;
    }

    // Декодируем JSON-ответ в массив
    $data = json_decode($response, true);

    try {
        // Извлекаем первый найденный объект с данными геокодера
        $geoObject = $data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject'];

        // Форматированный адрес из метаданных
        $formatted = $geoObject['metaDataProperty']['GeocoderMetaData']['text'];

        // Координаты в формате "долгота широта"
        $coords = $geoObject['Point']['pos'];
        [$lon, $lat] = explode(' ', $coords);

        // Запрос к API для поиска ближайшего метро по координатам
        $metroUrl = "https://geocode-maps.yandex.ru/1.x/?apikey=$apiKey&geocode=$lon,$lat&format=json&kind=metro&results=1";
        $metroResponse = file_get_contents($metroUrl);
        $metroData = json_decode($metroResponse, true);

        // Имя ближайшей станции метро или 'не найдено', если не нашли
        $metro = $metroData['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['name'] ?? 'не найдено';

        // Возвращаем данные в формате JSON
        echo json_encode([
            'formatted' => $formatted,
            'coords' => "$lat, $lon",
            'metro' => $metro
        ]);

    } catch (Exception $e) {
        // Если произошла ошибка при обработке данных — возвращаем ошибку
        echo json_encode(['error' => 'Ошибка при обработке данных']);
    }
}
?>
