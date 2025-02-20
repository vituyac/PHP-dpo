<?php

    function solve($data) {

        $lines = explode("\n", trim($data));
        $index = 0;

        $n = (int)trim($lines[$index++]);
        $bets = [];
        for ($i = 0; $i < $n; $i++) {
            $bets[] = explode(" ", trim($lines[$index++]));
        }

        $m = (int)trim($lines[$index++]);
        $games = [];
        for ($i = 0; $i < $m; $i++) {
            $games[] = explode(" ", trim($lines[$index++]));
        }

        $balance = 0;
        for ($i = 0; $i < $n; $i++) {
            if($games[$bets[$i][0]-1][4] != $bets[$i][2]) {
                $balance -= $bets[$i][1];
            }
            else {
                $temp = 0;
                if ($bets[$i][2] == 'L') {
                    $temp = 1;
                } elseif ($bets[$i][2] == 'R') {
                    $temp = 2;
                } elseif ($bets[$i][2] == 'D') {
                    $temp = 3;
                }
                $balance += (($bets[$i][1] * $games[$bets[$i][0]-1][$temp]) - $bets[$i][1]);
            }
        }
        return $balance;
    }
?>
