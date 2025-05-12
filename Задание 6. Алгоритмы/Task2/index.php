<?php

    function solve($data1, $data2) {
        // Загружаем XML из строк
        $productsXml = simplexml_load_string($data1);
        $sectionsXml = simplexml_load_string($data2);

        if ($productsXml === false || $sectionsXml === false) {
            return '';
        }

        // Сохраняем разделы
        $sections = [];
        foreach ($sectionsXml->Раздел as $section) {
            $id = (string)$section->Ид;
            $name = (string)$section->Наименование;
            $sections[$id] = [
                'name' => $name,
                'products' => []
            ];
        }

        // Добавляем товары в соответствующие разделы
        foreach ($productsXml->Товар as $product) {
            $productId = (string)$product->Ид;
            $productName = (string)$product->Наименование;
            $productArt = (string)$product->Артикул;

            foreach ($product->Разделы->ИдРаздела as $sectionId) {
                $sid = (string)$sectionId;
                if (isset($sections[$sid])) {
                    $sections[$sid]['products'][] = [
                        'id' => $productId,
                        'name' => $productName,
                        'art' => $productArt
                    ];
                }
            }
        }

        // Формируем итоговый XML
        $output = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><ЭлементыКаталога><Разделы/></ЭлементыКаталога>');

        foreach ($sections as $id => $section) {
            $sectionNode = $output->Разделы->addChild('Раздел');
            $sectionNode->addChild('Ид', $id);
            $sectionNode->addChild('Наименование', $section['name']);

            // Добавляем <Товары> даже если пусто
            $productsNode = $sectionNode->addChild('Товары');

            foreach ($section['products'] as $product) {
                $productNode = $productsNode->addChild('Товар');
                $productNode->addChild('Ид', $product['id']);
                $productNode->addChild('Наименование', $product['name']);
                $productNode->addChild('Артикул', $product['art']);
            }
        }

        return $output->asXML();
    }

?>