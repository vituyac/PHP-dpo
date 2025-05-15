<?php

    function buildCondition(string $key, $val): string {
        // 1) разбираем префикс
        $candidates = ['>=', '<=', '>', '<', '=', '!'];
        $op = '';
        $field = $key;
        foreach ($candidates as $cand) {
            if (strpos($key, $cand) === 0) {
                $op    = $cand;
                $field = substr($key, strlen($cand));
                break;
            }
        }
        // 2) если нет префикса — подставляем по типу
        if ($op === '') {
            if ($val === null)          $op = 'is';
            elseif (is_bool($val))      $op = 'is';
            elseif (is_string($val))    $op = 'like';
            else                         $op = '=';
        }

        // 3) строим строковое представление значения
        if ($val === null) {
            // null: "is null" или при '!' → "is not null"
            return $op === '!' 
                ? "$field is not null"
                : "$field is null";
        }
        if (is_bool($val)) {
            $b = $val ? 'true' : 'false';
            if ($op === '!')        return "$field is not $b";
            if ($op === 'is')       return "$field is $b";
            // если явный '=', '<', '>' и т.п.
                                    return "$field $op $b";
        }
        if (is_numeric($val)) {
            $n = $val + 0;
            if ($op === '!')        return "$field != $n";
                                    return "$field $op $n";
        }
        // для строк
        $s = addslashes($val);
        if ($op === '!')            return "$field != '$s'";
        if ($op === '=')            return "$field = '$s'";
        if (strtolower($op) === 'like') return "$field like '$s'";
        // сравнения <, <=, >, >= тоже работают со строками
                                    return "$field $op '$s'";
    }

    function buildWhere(array $node, string $logic = 'and'): string {
        $parts = [];
        foreach ($node as $key => $val) {
            // группа AND
            if (preg_match('/^and(_.*)?$/i', $key)) {
                $sub = buildWhere($val, 'and');
                if ($sub !== '') {
                    $parts[] = "($sub)";
                }
            }
            // группа OR
            elseif (preg_match('/^or(_.*)?$/i', $key)) {
                $sub = buildWhere($val, 'or');
                if ($sub !== '') {
                    $parts[] = "($sub)";
                }
            }
            // простое условие
            else {
                $parts[] = buildCondition($key, $val);
            }
        }
        return implode(" $logic ", $parts);
    }
    
    // Читаем и декодируем JSON
    $data = file_get_contents('Data/C.txt');
    $json = json_decode($data, true);

    // SELECT
    $select = '*';
    $select = implode(', ', $json['select']);

    // FROM
    $from = $json['from'];

    // WHERE
    $whereClause = '';
    if (!empty($json['where']) && is_array($json['where'])) {
        $w = buildWhere($json['where']);
        if ($w !== '') {
            $whereClause = "where $w\n";
        }
    }

    // ORDER BY
    $orderClause = '';
    if (!empty($json['order']) && is_array($json['order'])) {
        $parts = [];
        foreach ($json['order'] as $field => $dir) {
            $d = strtolower($dir) === 'desc' ? 'desc' : 'asc';
            $parts[] = "$field $d";
        }
        if ($parts) {
            $orderClause = "order by " . implode(', ', $parts) . "\n";
        }
    }

    // LIMIT
    $limitClause = '';
    if (isset($json['limit']) && is_numeric($json['limit'])) {
        $limitClause = "limit " . intval($json['limit']) . ";";
    } else {
        // если нет лимита — ставим точку с запятой в конце предыдущей строки
        $limitClause = ';';
    }

    // итоговый запрос
    $query  = "select $select\n";
    $query .= "from $from\n";
    $query .= $whereClause;
    $query .= $orderClause;
    $query .= $limitClause;

    echo $query;
    
