@extends('layouts.main')

@section('content')
    <div class="row">
        <div class="col-12 col-lg-6">
            @include('blocks.rounded_home_block',[
                'addClass' => 'half how-does-it-work',
                'head' => trans('content.how_does_it_work_head1'),
                'content' => trans('content.home_text_block4')
            ])
            @include('blocks.how_does_it_work_block',[
                'head' => '2. Обсудите детали в чате',
                'description' => 'Исполнители сами напишут вам в чат, где вы сможете обговорить все детали напрямую.',
                'imageDesc' => 'images/how_does_it_work/monitor2.png',
                'imageMob' => 'images/how_does_it_work/phone2.png',
            ])
        </div>
        <div class="col-12 col-lg-6">
            @include('blocks.how_does_it_work_block',[
                'head' => '1. Создайте заявку',
                'description' => 'Заполните все поля. чтобы исполнителю было проще откликнуться.',
                'imageDesc' => 'images/how_does_it_work/monitor1.png',
                'imageMob' => 'images/how_does_it_work/phone1.png',
            ])
            @include('blocks.how_does_it_work_block',[
                'head' => '3. Оцените исполнителя',
                'description' => 'После того как помощь оказана, завершите заявку и оцените исполнителя.',
                'imageDesc' => 'images/how_does_it_work/monitor3.png',
                'imageMob' => 'images/how_does_it_work/phone3.png',
            ])
        </div>
    </div>
@endsection
