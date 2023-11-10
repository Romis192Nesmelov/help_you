<div class="order-photo @error('avatar') error @enderror" style="background-image: url({{ asset($image ?? '') }});">
    <i class="icon-close2"></i>
    <i class="icon-file-plus2"></i>
    <img src="{{ asset('images/input_image_hover.svg') }}" />
    <input type="file" name="{{ $name }}">
</div>
@include('blocks.error_block', ['name' => $name])
