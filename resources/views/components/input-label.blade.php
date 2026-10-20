@props(['value'])

<label {{ $attributes->except('for')->merge(['class' => 'block font-medium text-sm text-gray-700']) }} for="{{ $attributes->get('for') }}">
    {{ $value ?? $slot }}
</label>
