@extends('layouts.admin')

@section('content')
    <div class="panel panel-flat">
        @include('admin.blocks.title_block')
        <div class="panel-body">
            <form class="form-horizontal" action="{{ route('admin.edit_partner') }}" enctype="multipart/form-data" method="post">
                @csrf
                @if (isset($partner))
                    @include('admin.blocks.hidden_id_block',['id' => $partner->id])
                @endif
                <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
                    @include('admin.blocks.input_image_block',[
                        'name' => 'logo',
                        'image' => isset($partner) ? $partner->logo : ''
                    ])
                </div>
                <div class="col-lg-10 col-md-9 col-sm-8 col-xs-12">
                    <div class="panel panel-flat">
                        <div class="panel-body">
                            @include('admin.blocks.input_block', [
                                'label' => 'Название партнера',
                                'name' => 'name',
                                'type' => 'text',
                                'max' => 50,
                                'placeholder' => 'Название партнера',
                                'value' => isset($partner) ? $partner->name : ''
                            ])
                            @include('admin.blocks.textarea_block',[
                                'label' => 'Описание',
                                'name' => 'about',
                                'max' => 3000,
                                'value' => isset($partner) ? $partner->about : '',
                            ])
                            @include('admin.blocks.textarea_block',[
                                'label' => 'Информация о партнере',
                                'name' => 'info',
                                'max' => 50000,
                                'value' => isset($partner) ? $partner->info : '',
                            ])
                            @include('admin.blocks.save_button_block')
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
