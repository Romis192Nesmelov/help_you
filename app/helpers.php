<?php

function visibleStep($step, $session_key): bool
{
    return ($step == 1 && !session()->has($session_key)) || (session()->has($session_key) && $step == count(session()->get($session_key))+1);
}

function getStepClass($step, $session_key): string
{
    return visibleStep($step, $session_key) ? 'd-block' : 'd-none';
}

function getStepProgress($session_key): string
{
    return (session()->has($session_key) ? count(session()->get($session_key)) * 25 : 0).'%';
}

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
