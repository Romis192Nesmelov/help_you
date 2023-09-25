@include('blocks.input_block',[
    'name' => 'password',
    'addClass' => 'password',
    'type' => 'password',
    'label' => $label ?? trans('auth.password'),
    'icon' => 'icon-eye',
    'ajax' => true
])
@include('blocks.input_block',[
    'name' => 'password_confirmation',
    'addClass' => 'password',
    'type' => 'password',
    'label' => trans('auth.confirm_password'),
    'icon' => 'icon-eye',
    'ajax' => true
])
