<div class="rounded-block half how-does-it-work">
    <h3 class="text-center">{{ $head }}</h3>
    <div class="w-100 text-center d-none d-md-block d-sm-none">
        <a class="fancybox" href="{{ asset($imageDesc) }}">
            <img src="{{ asset($imageDesc) }}" />
        </a>
    </div>
    <div class="w-100 text-center d-md-none d-sm-block">
        <a class="fancybox" href="{{ asset($imageMob) }}">
            <img src="{{ asset($imageMob) }}" />
        </a>
    </div>
    <p class="text-center">{{ $description }}</p>
</div>
