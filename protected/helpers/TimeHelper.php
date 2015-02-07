<?php

/**
 * Helper для работы с датами
 */
class TimeHelper
{

	/**
	 * Форматируем время
     */
    public static function format($time) {
        setlocale(LC_ALL, 'ru_RU.UTF-8');
        
        return strftime('%d %b %Y %H:%M:%S', strtotime($time));
    }
}