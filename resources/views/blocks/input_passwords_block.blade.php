@include('blocks.input_block',[
    'name' => 'password',
    'addClass' => 'password',
    'type' => 'password',
    'label' => $labelPassword ?? trans('auth.password'),
    'icon' => 'icon-eye',
    'ajax' => true
])
@include('blocks.input_block',[
    'name' => 'password_confirmation',
    'addClass' => 'password',
    'type' => 'password',
    'label' => $labelConfirmPassword ?? trans('auth.confirm_password'),
    'icon' => 'icon-eye',
    'ajax' => true
])
