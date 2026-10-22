@props(['name', 'label', 'value' => null, 'rows' => 4, 'hint' => null])
<div class="field">
    <label for="f-{{ $name }}">{{ $label }}</label>
    <textarea id="f-{{ $name }}" name="{{ $name }}" rows="{{ $rows }}" {{ $attributes }}>{{ old($name, $value) }}</textarea>
    @if($hint)<div class="hint">{{ $hint }}</div>@endif
    @error($name)<div class="err">{{ $message }}</div>@enderror
</div>
