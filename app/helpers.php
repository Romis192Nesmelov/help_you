<?php
use \Illuminate\Database\Eloquent\Model;

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

function getItemName(Model $model): string
{
    $name = '';
    foreach (['name','family','phone','email'] as $k => $field) {
        if (isset($model[$field])) {
            $name .= $name ? ' '.$model[$field] : $model[$field];
            if ($k) break;
        }
    }
    return $name;
}
