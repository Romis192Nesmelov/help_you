<div {{ $attributes->class('modal fade') }} id="{{ $attributes->get('id') }}" tabindex="-1" aria-labelledby="{{ $attributes->get('id') }}Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                @if ($attributes->has('head'))
                    <h4 class="modal-title fs-5">{{ $attributes->get('head') }}</h4>
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="{{ trans('content.close') }}"></button>
            </div>
            <div class="modal-body">
                {{ $slot }}
            </div>
            @if ($attributes->has('footer') && $attributes->get('footer'))
                <div class="modal-footer">
                    @if ($attributes->has('yes_button') && $attributes->get('yes_button'))
                        @include('blocks.button_block',[
                            'id' => null,
                            'buttonType' => 'button',
                            'primary' => true,
                            'addClass' => 'm-auto mt-3 delete-yes',
                            'buttonText' => trans('content.yes')
                        ])
                        @include('blocks.button_block',[
                            'id' => null,
                            'primary' => true,
                            'dataDismiss' => true,
                            'addClass' => 'm-auto mt-3',
                            'buttonText' => trans('content.no')
                        ])
                    @else
                        @include('blocks.button_block',[
                            'id' => null,
                            'primary' => true,
                            'dataDismiss' => true,
                            'addClass' => 'm-auto mt-3',
                            'buttonText' => trans('content.close')
                        ])
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

