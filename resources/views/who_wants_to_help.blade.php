@extends('layouts.main')

@section('content')
    <div class="row">
        <div class="col-12 col-lg-6">
            @include('blocks.rounded_home_block',[
                'addClass' => 'half how-does-it-work',
                'head' => trans('content.how_does_it_work_head2'),
                'content' => trans('content.home_text_block4')
            ])
            @include('blocks.how_does_it_work_block',[
                'head' => '2. Обсудите детали в чате',
                'description' => 'Обговорите все детали в чате напрямую и окажите помощь',
                'imageDesc' => 'images/how_does_it_work/desktop5.jpg',
                'imageMob' => 'images/how_does_it_work/mobile5.jpg',
            ])
        </div>
        <div class="col-12 col-lg-6">
            @include('blocks.how_does_it_work_block',[
                'head' => '1. Выберете, кому хотите помочь',
                'description' => 'Выберете точку на карте, или воспользуйтесь поиском, чтобы найти подходящий запрос о помощи и откликнуться на него.',
                'imageDesc' => 'images/how_does_it_work/desktop4.jpg',
                'imageMob' => 'images/how_does_it_work/mobile4.jpg',
            ])
            @include('blocks.how_does_it_work_block',[
                'head' => '3. Воспользуйтесь подарком',
                'description' => 'Получете поощрение от партнеров платформы.',
                'imageDesc' => 'images/how_does_it_work/desktop6.jpg',
                'imageMob' => 'images/how_does_it_work/mobile6.jpg',
            ])
        </div>
    </div>
@endsection
