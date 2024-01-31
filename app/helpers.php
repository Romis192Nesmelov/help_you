<?php
function getRussianDate($timestamp): string
{
    $monthList = [
        'января',
        'февраля',
        'марта',
        'апреля',
        'мая',
        'июня',
        'июля',
        'августа',
        'сентября',
        'октября',
        'ноября',
        'декабря'
    ];
    return date('d',$timestamp).' '. $monthList[(int)date('n',$timestamp) - 1] . ' ' . date('Y',$timestamp);
}
