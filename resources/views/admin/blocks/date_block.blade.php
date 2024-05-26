<div id="date-{{ $name }}" class="date form-group has-feedback">
    <label class="control-label col-md-12 text-semibold">{{ $label }}</label>
    <div class="input-group">
        <span class="input-group-addon"><i class="icon-calendar22"></i></span>
        <input type="text" name="{{ $name }}" class="form-control daterange-single" value="{{ !count($errors) ? date('d.m.Y', $value) : old($name) }}">
    </div>
    <div class="error {{ $name }}"></div>
</div>
