<div class="order-photo @error($name) error @enderror" style="background-image: url({{ $image ? asset($image) : '' }});">
    <i class="icon-close2 {{ $image ? 'd-block' : '' }}"></i>
    <i class="icon-file-plus2 {{ $image ? 'd-none' : '' }}"></i>
    <img src="{{ asset('images/input_image_hover.svg') }}" />
    <input type="file" name="{{ $name }}">
</div>
@include('blocks.error_block', ['name' => $name])
