<button
    {{ isset($id) && $id ? 'id='.$id : '' }}
    type="{{ isset($buttonType) && $buttonType ? $buttonType : 'button' }}"
    class="btn btn-{{ isset($primary) && $primary ? 'primary' : 'secondary' }}
    {{ isset($arrowIcon) && $arrowIcon ? 'withArrow' : '' }}
    {{ isset($addClass) && $addClass ? $addClass : '' }}"

    @if (isset($dataScroll) && $dataScroll)
        data-scroll="{{ $dataScroll }}"
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
