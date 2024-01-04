<?php

use App\Models\User;

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

function avatarProps($avatar, $avatarProps, $coof): string
{
    $avatarStyles = 'background-image:url('.(asset($avatar ? $avatar : 'images/def_avatar.svg')).');';
    if ($avatarProps) {
        foreach ($avatarProps as $prop => $value) {
            if ($prop != 'background-size') $avatarStyles .= $prop.':'.($value * $coof).';';
            else $avatarStyles .= $prop.':'.$value.';';
        }
    }
    return $avatarStyles;
}

function getUserAge(User $user): string
{
    $age = $user->years();
    if ($age == 1) $word = 'год';
    elseif ($age > 1 && $age < 5) $word = 'года';
    elseif ($age >= 5 && $age < 21) $word = 'лет';
    else {
        $lastDigit = (int)substr($age, -1, 1);
        if ($lastDigit == 0) $word = 'лет';
        elseif ($lastDigit == 1) $word = 'год';
        elseif ($lastDigit > 1 && $lastDigit < 5) $word = 'года';
        else $word = 'лет';
    }
    return $age.' '.$word;
}

function getUserRating(User $user): int
{
    if (!$user->ratings->count()) return 0;
    else {
        $totalRating = 0;
        foreach ($user->ratings as $rating) {
            $totalRating += $rating->value;
        }
        return round($totalRating / $user->ratings->count());
    }
}
