@props(['disabled' => false, 'id'])

<input id="{{ $id }}" @disabled($disabled) {{ $attributes->except('id')->merge(['class' => 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) }}>
