<?php

function visibleStep($step): bool
{
    return ($step == 1 && !session()->has('steps')) || (session()->has('steps') && $step == count(session()->get('steps'))+1);
}

function getStepClass($step): string
{
    return visibleStep($step) ? 'd-block' : 'd-none';
}

function getStepProgress(): string
{
    return (session()->has('steps') ? count(session()->get('steps')) * 25 : 0).'%';
}
