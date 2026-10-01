@props(['name', 'label', 'type' => 'text', 'value' => null])
<div {{ $attributes->only('class') }}>
    <label for="{{ $name }}" class="label">{{ $label }}</label>
    <input id="{{ $name }}" type="{{ $type }}" name="{{ $name }}" value="{{ old($name, $value) }}"
        {{ $attributes->except('class')->merge(['required' => true]) }}
        @error($name) aria-invalid="true" @enderror
        class="field @error($name) border-red-400 @enderror">
    @error($name)<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
</div>
