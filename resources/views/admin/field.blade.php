@php
    $type = $type ?? 'text';
    $required = $required ?? false;
    $current = old($name, $value ?? data_get($record ?? null, $name) ?? $default ?? '');
    if (is_array($current)) { $current = ''; }
    if ($type === 'datetime-local' && $current) {
        try { $current = \Carbon\Carbon::parse($current)->format('Y-m-d\TH:i'); }
        catch (\Throwable $exception) { $current = ''; }
    }
@endphp
<div class="field {{ $type === 'checkbox' ? 'field-checkbox' : '' }} {{ !empty($wide) ? 'field-wide' : '' }}">
    @if($type === 'checkbox')
        <input type="hidden" name="{{ $name }}" value="0"><label for="field-{{ $name }}"><input type="checkbox" id="field-{{ $name }}" name="{{ $name }}" value="1" @checked((bool) $current) @if(!empty($help)) aria-describedby="help-{{ $name }}" @endif> <span>{{ $label }}</span></label>
    @else
        <label for="field-{{ $name }}">{{ $label }} @if($required)<span aria-hidden="true">*</span>@endif</label>
        @if($type === 'textarea')<textarea id="field-{{ $name }}" name="{{ $name }}" rows="{{ $rows ?? 6 }}" @if($required) required @endif maxlength="{{ $max ?? 50000 }}" @if(!empty($help)) aria-describedby="help-{{ $name }}" @endif>{{ $current }}</textarea>
        @elseif($type === 'select')<select id="field-{{ $name }}" name="{{ $name }}" @if($required) required @endif @if(!empty($help)) aria-describedby="help-{{ $name }}" @endif><option value="">{{ $placeholder ?? 'Pilih…' }}</option>@foreach($options as $option => $caption)<option value="{{ $option }}" @selected((string) $current === (string) $option)>{{ $caption }}</option>@endforeach</select>
        @else<input type="{{ $type }}" id="field-{{ $name }}" name="{{ $name }}" value="{{ $current }}" @if($required) required @endif @if(!in_array($type, ['number','datetime-local'], true)) maxlength="{{ $max ?? 200 }}" @endif @if($type === 'number') min="{{ $min ?? 0 }}" max="{{ $max ?? 9999 }}" @endif @if(!empty($help)) aria-describedby="help-{{ $name }}" @endif>@endif
    @endif
    @if(!empty($help))<p class="field-help" id="help-{{ $name }}">{{ $help }}</p>@endif
    @error($name)<p class="field-error">{{ $message }}</p>@enderror
</div>
