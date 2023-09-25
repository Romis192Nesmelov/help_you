@include('blocks.input_block',[
    'name' => 'code',
    'addClass' => 'd-none',
    'placeholder' => '__-__-__',
    'label' => trans('auth.code'),
    'icon' => 'icon-shield2',
    'ajax' => true
])
