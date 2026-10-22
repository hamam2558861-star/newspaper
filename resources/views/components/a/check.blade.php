@props(['name', 'label', 'checked' => false, 'hint' => null, 'value' => '1'])
<label class="check">
    <input type="checkbox" name="{{ $name }}" value="{{ $value }}" @checked(old('_token') ? old($name) !== null : $checked) {{ $attributes }}>
    <span>{{ $label }}@if($hint)<small>{{ $hint }}</small>@endif</span>
</label>
