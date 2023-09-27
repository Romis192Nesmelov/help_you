<nav id="{{ $id }}" class="main-nav navbar navbar-expand-lg navbar-light">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-{{ $id }}" aria-controls="navbar-{{ $id }}" aria-expanded="false" aria-label="Переключатель навигации">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbar-{{ $id }}">
        <ul class="navbar-nav">
            @if (!auth()->guest())
                @include('blocks.nav-item_block', [
                    'menuItemKey' => 'messages',
                    'menuItem' => ['href' => true],
                    'addClass' => 'd-block d-sm-none'
                ])
            @endif
            @foreach($menu as $menuItemKey => $menuItem)
                @include('blocks.nav-item_block')
            @endforeach
            @if (!auth()->guest())
                @include('blocks.nav-item_block', [
                    'menuItemKey' => 'account',
                    'menuItem' => ['href' => true],
                    'addClass' => 'd-block d-sm-none'
                ])
            @endif
        </ul>
    </div>
</nav>
