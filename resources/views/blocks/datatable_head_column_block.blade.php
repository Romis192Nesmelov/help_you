<div class="head">@if (isset($grandName)) {{ $item->$grandName }} – @endif{{ isset($relationHead) ? $item->$relationHead->$headName : $item->$headName }}</div>
<div class="content">{{ isset($relationContent) ? $item->$relationContent->$contentName : $item->$contentName }}</div>
