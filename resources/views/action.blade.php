@extends('layouts.main')

@section('content')
<div class="rounded-block p-5 w-100 h-auto">
    <h1 class="text-center mb-4">{{ $action->name }}</h1>
{{--    <h3 class="text-center mb-2">Подарок от компании <span class="text-uppercase">«{{ $action->partner->name }}»</span></h3>--}}
    <h3 class="text-center mb-5">Информация о подарке</h3>
    {!! $action->html !!}
</div>
@endsection
