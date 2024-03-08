@extends('layouts.main')

@section('content')
<div class="rounded-block p-5 w-100 h-auto">
    <h1 class="text-center mb-4">{{ $action->name }}</h1>
    {!! $action->html !!}
</div>
@endsection
