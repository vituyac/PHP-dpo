<?php

    function solve($data1, $data2) {
        // преобразования строки с XML-данными в объект типа SimpleXMLElement
        $productsXml = simplexml_load_string($data1);
        $sectionsXml = simplexml_load_string($data2);

        if ($productsXml === false || $sectionsXml === false) {
            return '';
        }

        // Сохраняем разделы
        $sections = [];
        foreach ($sectionsXml->Раздел as $section) {
            $id = (string)$section->Ид; // Получаем ID раздела
            $name = (string)$section->Наименование; // Получаем название раздела
            $sections[$id] = [ // Добавляем в массив разделов по ID
                'name' => $name, // Название раздела
                'products' => [] // Пустой массив товаров в этом разделе
            ];
        }

        // Привязываем товары к соответствующим разделам
        foreach ($productsXml->Товар as $product) {
            $productId = (string)$product->Ид; // ID товара
            $productName = (string)$product->Наименование; // Название товара
            $productArt = (string)$product->Артикул; // Артикул товара

            // Товар может относиться к нескольким разделам
            foreach ($product->Разделы->ИдРаздела as $sectionId) {
                $sid = (string)$sectionId; // ID текущего раздела товара
                if (isset($sections[$sid])) { // Проверяем, существует ли такой раздел
                    // Добавляем товар в массив products раздела
                    $sections[$sid]['products'][] = [
                        'id' => $productId,
                        'name' => $productName,
                        'art' => $productArt
                    ];
                }
            }
        }

        // Формируем итоговый XML-документ
        $output = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><ЭлементыКаталога><Разделы/></ЭлементыКаталога>');

        // Заполняем XML на основе собранных данных
        foreach ($sections as $id => $section) {
            // Добавляем узел <Раздел>
            $sectionNode = $output->Разделы->addChild('Раздел');
            $sectionNode->addChild('Ид', $id); // ID раздела
            $sectionNode->addChild('Наименование', $section['name']); // Название раздела

            // Добавляем узел <Товары> (даже если пустой, чтобы структура была полной)
            $productsNode = $sectionNode->addChild('Товары');

            // Добавляем каждый товар в раздел
            foreach ($section['products'] as $product) {
                $productNode = $productsNode->addChild('Товар');
                $productNode->addChild('Ид', $product['id']); // ID товара
                $productNode->addChild('Наименование', $product['name']); // Название товара
                $productNode->addChild('Артикул', $product['art']); // Артикул товара
            }
        }

        // Возвращаем готовый XML как строку
        return $output->asXML();
    }

?>