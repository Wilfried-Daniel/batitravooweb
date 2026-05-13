@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'required' => false,
    'options' => null,
    'placeholder' => null,
    'hint' => null,
    'autocomplete' => null,
    'rows' => 4,
    'maxWidth' => '100%',
])

@php
    $id = $attributes->get('id') ?? 'f_'.$name;
    $errorKey = str_replace(['[]', '[', ']'], ['', '.', ''], $name);
    $hasError = $errors->has($errorKey);
    $val = old($errorKey, $value);
@endphp

<div class="admin-field {{ $hasError ? 'admin-field--error' : '' }}" style="max-width:{{ $maxWidth }}">
    <label for="{{ $id }}" class="admin-field__label">
        {{ $label }}
        @if($required)
            <span class="admin-field__req" aria-label="obligatoire">*</span>
        @endif
    </label>

    @if ($type === 'select' && is_array($options))
        <select
            id="{{ $id }}"
            name="{{ $name }}"
            class="admin-field__control admin-field__select"
            @if($required) required @endif
            {{ $attributes->except(['id', 'class']) }}
        >
            @foreach($options as $optVal => $optLabel)
                <option value="{{ $optVal }}" @selected((string) $val === (string) $optVal)>{{ $optLabel }}</option>
            @endforeach
        </select>
    @elseif ($type === 'textarea')
        <textarea
            id="{{ $id }}"
            name="{{ $name }}"
            class="admin-field__control admin-field__textarea"
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            rows="{{ $rows }}"
            @if($required) required @endif
            {{ $attributes->except(['id', 'class']) }}
        >{{ $val }}</textarea>
    @else
        <input
            id="{{ $id }}"
            type="{{ $type }}"
            name="{{ $name }}"
            value="{{ $val }}"
            class="admin-field__control admin-field__input"
            @if($placeholder) placeholder="{{ $placeholder }}" @endif
            @if($required) required @endif
            @if($autocomplete) autocomplete="{{ $autocomplete }}" @endif
            {{ $attributes->except(['id', 'class']) }}
        >
    @endif

    @if($hasError)
        <p class="admin-field__error" role="alert">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            {{ $errors->first($errorKey) }}
        </p>
    @elseif($hint)
        <p class="admin-field__hint">{{ $hint }}</p>
    @endif
</div>
