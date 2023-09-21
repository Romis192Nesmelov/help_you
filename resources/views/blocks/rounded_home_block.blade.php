<div {{ isset($id) ? 'id='.$id : '' }} class="rounded-block {{ $addClass }}">
    <h1><div>{!! $head !!}</div></h1>
    <div class="bottom-block">
        @if (is_array($content))
            @foreach ($content as $item)
                <p>{{ $item }}</p>
            @endforeach
        @else
            <p>{{ $content }}</p>
        @endif
        <div class="d-flex align-items-center {{ isset($addLink) ? 'justify-content-between' : 'justify-content-end' }} ">
            @if (isset($addLink))
                <a href="{{ $addLink }}">{{ $addLinkText }}</a>
            @endif
            @include('blocks.rounded_link_block', ['link' => $link])
        </div>
    </div>
</div>
