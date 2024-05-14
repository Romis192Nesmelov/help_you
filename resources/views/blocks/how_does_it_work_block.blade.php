<div class="rounded-block half p-2 how-does-it-work">
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
    <h3 class="text-center">{{ $head }}</h3>
    <p class="text-center m-0">{{ $description }}</p>
</div>
