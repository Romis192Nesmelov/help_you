<div {{ isset($id) ? 'id='.$id : '' }} class="rounded-block {{ $addClass }}">
    <h1><div>{!! $head !!}</div></h1>
    @if (isset($image))
        <div class="w-100 pt-5 image">
            <img src="{{ asset($image) }}" />
        </div>
    @endif
    <div class="bottom-block">
        @if (isset($content) && is_array($content))
            @foreach ($content as $item)
                <p>{{ $item }}</p>
            @endforeach
        @elseif (isset($content))
            <p>{{ $content }}</p>
        @endif
        <div class="d-flex align-items-center {{ isset($addLink) ? 'justify-content-between' : 'justify-content-end' }} ">
            @if (isset($addLink))
                <a href="{{ $addLink }}">{{ $addLinkText }}</a>
            @endif
            @if (isset($link) && $link)
                @include('blocks.rounded_link_block', ['link' => $link])
            @endif
        </div>
    </div>
</div>
