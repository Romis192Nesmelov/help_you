@include('admin.blocks.checkbox_block', [
    'name' => 'active',
    'checked' => $checked,
    'label' => trans('admin.active')
])
