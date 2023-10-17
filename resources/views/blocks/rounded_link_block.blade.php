<a class="link-cover" {{ isset($id) ? 'id='.$id : '' }} href="{{ $link }}">
    <div class="link">
        @if (isset($buttonText))
            <span>{{ $buttonText }}</span>
        @else
            <i class="{{ isset($inverse) && $inverse ? 'icon-arrow-left7' : 'icon-arrow-right7' }}"></i>
        @endif
    </div>
</a>
