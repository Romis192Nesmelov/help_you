@foreach ($items as $item)
    <div class="form-check mb-1 {{ $addClass ?? '' }}">
        <input
            class="form-check-input"
            id="{{ $idPrefix.$item['id'] }}"
            type="radio"
            name="{{ $name }}"
            value="{{ $item['id'] }}"
            {{ isset($checked) && $checked == $item['id'] ? 'checked=checked' : ($loop->first ? 'checked=checked' : '') }}
        >
        <label class="form-check-label" for="{{ $idPrefix.$item['id'] }}">{{ $item[$option] }}</label>
    </div>
@endforeach
