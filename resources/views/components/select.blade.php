@props(['name', 'label', 'options', 'value' => null, 'keyed' => false, 'placeholder' => null])

<div {{ $attributes->only('class') }}>
    <label for="{{ $name }}" class="label">
        {{ $label }}
    </label>
    <select id="{{ $name }}" name="{{ $name }}" @required($placeholder) class="field
    @error($name)
        border-red-400
    @enderror
    ">
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        @foreach ($options as $key => $option)
            @php
                $v = $keyed ? $key : (is_object($option) ? $option->value : $option);
                $text = $keyed ? $option : $v;
            @endphp
            <option value="{{ $v }}" @selected(old($name, $value) == $v)>{{ $text }}</option>
        @endforeach
    </select>
    @error ($name)
        <p class="mt-1 text-sm text-red-700">
            {{ $message }}
        </p>
    @enderror
</div>