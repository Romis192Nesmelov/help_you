<?php if (isset($addAttr) && is_array($addAttr)) {
    $attrStr = '';
    foreach ($addAttr as $attr => $value) {
        $attrStr .= $attr.'="'.$value.'"';
    }
}
?>
<button {{ isset($id) && $id ? 'id='.$id : '' }} type="{{ isset($buttonType) && $buttonType ? $buttonType : 'button' }}"
    class="btn btn-{{ isset($primary) && $primary ? 'primary' : 'secondary' }} {{ isset($addClass) && $addClass ? $addClass : '' }}"
    @if (isset($attrStr) && $attrStr)
        {!! $attrStr !!}
    @endif

    @if (isset($dataTarget) && $dataTarget)
        data-bs-toggle="modal" data-bs-target="#{{ $dataTarget }}"
    @elseif (isset($disabled) && $disabled)
        disabled="disabled"
    @endif

    @if (isset($dataDismiss) && $dataDismiss)
        data-bs-dismiss="modal"
        data-dismiss="modal"
    @endif
>
    @if (isset($icon))
        <i class="{{ $icon }}"></i>
    @endif
    <span>{{ $buttonText }}</span>
</button>
