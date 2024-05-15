@foreach ($values as $value)
    <div class="form-group {{ $addClass ?? '' }}">
        <div class="col-{{ $col ?? '12' }}">
            <input class="radio-input" value="{{ $value['val'] }}" type="radio" name="{{ $name }}" {{ $activeValue == $value['val'] || (count($errors) && $value['val'] == old($name)) ? 'checked' : '' }}>
            <span class="control-label">{!! $value['descript'] !!}</span>
            @if ($errors && $errors->has($name))
                <div class="form-control-feedback">
                    <i class="icon-cancel-circle2"></i>
                </div>
            @endif
        </div>
    </div>
@endforeach
@include('blocks.error_block')
