@props(['name', 'label', 'options', 'value' => null])

<div {{ $attributes->only('class') }}>
    <label for="{{ $name }}" class="label">
        {{ $label }}
    </label>
    <select id="{{ $name }}" name="{{ $name }}" class="field
    @error($name)
        border-red-400
    @enderror
    ">
        @foreach ($options as $option)
            @php $v = is_object($option) ? $option->value : $option; @endphp
            <option value="{{ $v }}" @selected(old($name, $value) == $v)>{{ $v }}</option>
        @endforeach
    </select>
    @error ($name)
        <p class="mt-1 text-sm text-red-700">
            {{ $message }}
        </p>
    @enderror
</div>
