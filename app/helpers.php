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
