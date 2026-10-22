@props(['name', 'label', 'type' => 'text', 'value' => null, 'hint' => null, 'required' => false])
<div class="field">
    <label for="f-{{ $name }}">{{ $label }}@if($required) <span style="color:var(--red)">*</span>@endif</label>
    <input id="f-{{ $name }}" type="{{ $type }}" name="{{ $name }}" @if($type !== 'password' && $type !== 'file') value="{{ old($name, $value) }}" @endif {{ $required ? 'required' : '' }} {{ $attributes }}>
    @if($hint)<div class="hint">{{ $hint }}</div>@endif
    @error($name)<div class="err">{{ $message }}</div>@enderror
</div>
