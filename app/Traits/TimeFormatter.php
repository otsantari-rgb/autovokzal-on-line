<?php

namespace App\Traits;

trait TimeFormatter
{
    public function getTimeWord(int $time): string
    {
        $days = intdiv($time, 1440); // Количество полных дней
        $remainingMinutes = $time % 1440; // Оставшиеся минуты
        $hours = intdiv($remainingMinutes, 60); // Количество полных часов в оставшихся минутах
        $minutes = $remainingMinutes % 60; // Оставшиеся минуты после подсчета часов

        // Формируем строку для дней, часов и минут
        $timeParts = [];

        // Функция для корректного вывода слова в зависимости от числа
        $getWord = function (int $value, array $forms): string {
            $lastDigit = $value % 10;
            $lastTwoDigits = $value % 100;

            if ($lastDigit == 1 && $lastTwoDigits != 11) {
                return $forms[0]; // Например: 'день', 'час', 'минута'
            } elseif (in_array($lastDigit, [2, 3, 4]) && ! in_array($lastTwoDigits, [12, 13, 14])) {
                return $forms[1]; // Например: 'дня', 'часа', 'минуты'
            } else {
                return $forms[2]; // Например: 'дней', 'часов', 'минут'
            }
        };

        if ($time !== 1440 and $time !== 30) {
            // Добавляем дни, если они есть
            if ($days > 0) {
                $timeParts[] = "$days " . $getWord($days, ['день', 'дня', 'дней']);
            }

            // Добавляем часы, если они есть
            if ($hours > 0) {
                $timeParts[] = "$hours " . $getWord($hours, ['час', 'часа', 'часов']);
            }

            // Добавляем минуты, если они есть
            if ($minutes > 0) {
                $timeParts[] = "$minutes " . $getWord($minutes, ['минута', 'минуты', 'минут']);
            }
        } else {
            if ($time == 1440) {
                $timeParts[] = 'сутки';
            } else {
                $timeParts[] = 'полчаса';
            }
        }

        return implode(' ', $timeParts);
    }
}
