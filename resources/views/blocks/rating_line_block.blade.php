<div class="rating-line">
    @for ($i=1;$i<=5;$i++)
        @if (isset($rating) && $i <= $rating)
            <i id="rating-star-{{ $i }}" class="icon-star-full2"></i>
        @else
            <i id="rating-star-{{ $i }}" class="icon-star-empty3"></i>
        @endif
    @endfor
</div>
