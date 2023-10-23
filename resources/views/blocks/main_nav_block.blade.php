<nav id="{{ $id }}" class="main-nav navbar navbar-expand-lg navbar-light">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-{{ $id }}" aria-controls="navbar-{{ $id }}" aria-expanded="false" aria-label="Переключатель навигации">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbar-{{ $id }}">
        <ul class="navbar-nav">
            @if (auth()->check())
                @include('blocks.nav-item_block', [
                    'menuItem' => 'account.messages',
                    'menuName' => trans('menu.messages'),
                    'addClass' => 'd-block d-sm-none'
                ])
            @endif
            @foreach($mainMenu as $menuItem)
                @include('blocks.nav-item_block',[
                    'menuItem' => $menuItem,
                    'menuName' => trans('menu.'.$menuItem),
                ])
            @endforeach
            @if (auth()->check())
                @include('blocks.nav-item_block', [
                    'menuItem' => 'account.change',
                    'menuName' => trans('menu.account'),
                    'addClass' => 'd-block d-sm-none'
                ])
                @include('blocks.nav-item_block', [
                    'menuItem' => 'order.new_order',
                    'menuName' => trans('menu.make_an_order'),
                    'addClass' => 'd-block d-sm-none'
                ])
            @endif
        </ul>
    </div>
</nav>
