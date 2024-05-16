@props([
    'head' => false,
    'no_header' => false,
    'footer' => false,
    'yes_button' => false,
    'yes_button_id' => null,
    'del_function' => null,
    'yes_button_class' => 'delete-yes',
    'yes_button_text' => trans('content.yes'),
    'show' => false
])

<div {{ $attributes->class('modal fade') }} tabindex="-1" aria-labelledby="{{ $attributes->get('id') }}Label" aria-hidden="true" {{ $del_function ? 'del_function='.$del_function : ''}}>
    <div class="modal-dialog">
        <div class="modal-content">
            @if (!$no_header)
                <div class="modal-header">
                    @if ($head)
                        <div class="modal-title fs-5 text-center w-100">{{ $head }}</div>
                    @endif
                    <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="{{ trans('content.close') }}"></button>
                </div>
            @endif
            <div class="modal-body">
                {{ $slot }}
            </div>
            @if ($footer)
                <div class="modal-footer d-flex justify-content-center">
                    @if ($yes_button)
                        @include('admin.blocks.button_block',[
                            'id' => $yes_button_id,
                            'buttonType' => 'button',
                            'primary' => true,
                            'addClass' => 'w-25 mt-3 '.$yes_button_class,
                            'buttonText' => $yes_button_text
                        ])
                        @include('admin.blocks.button_block',[
                            'id' => null,
                            'primary' => true,
                            'dataDismiss' => true,
                            'addClass' => 'w-25 mt-3',
                            'buttonText' => trans('content.no')
                        ])
                    @else
                        @include('admin.blocks.button_block',[
                            'id' => null,
                            'primary' => true,
                            'dataDismiss' => true,
                            'addClass' => 'w-50 m-auto mt-3',
                            'buttonText' => trans('content.close')
                        ])
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

