@props(['name', 'label', 'cases', 'selected' => []])
<fieldset {{ $attributes->only('class') }}>
    <legend class="label">{{ $label }}</legend>
    <div class="flex flex-wrap gap-2">
        @foreach ($cases as $case)
        @php $v = is_object($case) ? $case->value : $case; @endphp
        <label class="cursor-pointer">
            <input type="checkbox" name="{{ $name }}[]" value="{{ $v }}" @checked(in_array($v, (array) $selected)) class="peer sr-only">
            <span class="badge badge-neutral px-2.5 py-1 peer-checked:bg-reagent-600 peer-checked:text-white peer-checked:ring-reagent-600 peer-focus-visible:outline-2 peer-focus-visible:outline-reagent-600">{{ $v }}</span>
        </label>
        @endforeach
    </div>
    @error($name)<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
    @error($name . '.*')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
</fieldset>
