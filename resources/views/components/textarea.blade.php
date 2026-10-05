@props(['name', 'label', 'value' => null, 'required' => false])

<div {{ $attributes->only('class') }}>
    <label for="{{ $name }}" class="label">
        {{ $label }}
    </label>
    <textarea id="{{ $name }}" name="{{ $name }}" rows="4"
    @required ($required)
        {{ $attributes->except('class') }}
        class="field
        @error($name)
            border-red-400
        @enderror
        ">
        {{ old($name, $value) }}
    </textarea>
    @error ($name)
        <p class="mt-1 text-sm text-red-700">
            {{ $message }}
        </p>
    @enderror
</div>
