@props(['value', 'size' => 150])

@php $svgClass = $attributes->get('class', ''); @endphp

{!! str_replace('<svg ', '<svg class="' . e($svgClass) . '" ', qr_code_svg($value, $size)) !!}
