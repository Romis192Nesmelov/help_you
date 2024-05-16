<x-incover
    name="{{ $name }}"
    error="{{ count($errors) && $errors->has($name) ? $errors->first($name) : '' }}"
    label="{{ isset($label) && $label ? $label : ''  }}"
>
    <textarea
        class="form-control {{ isset($simple) && $simple ? 'simple' : '' }} @error($name) error @enderror"
        name="{{ $name }}"
        placeholder="{{ isset($placeholder) && $placeholder ? $placeholder : '' }}"
        {{ isset($max) && $max ? 'maxlength='.$max : '' }}
        {{ isset($disabled) && $disabled ? 'disabled=disabled' : '' }}
    >
        {{ count($errors) ? old($name) : (isset($value) ? $value : '') }}
    </textarea>
</x-incover>
{{--@if (!isset($simple) || !$simple)--}}
{{--    <script>--}}
{{--        var editor = CKEDITOR.replace('{{ $name }}', {--}}
{{--            height: '{{ isset($height) ? $height.'px' : '300px' }}'--}}
{{--        });--}}
{{--    </script>--}}
{{--@endif--}}
