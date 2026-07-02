{{-- Logo Speed Cloud — nuage souriant + connectiques. $color et $size configurables. --}}
@php($color = $color ?? '#8a4dfd')
@php($size = $size ?? 30)
<svg width="{{ $size }}" height="{{ round($size * 122 / 130) }}" viewBox="0 0 130 122" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="{{ $color }}" stroke-width="8.5" stroke-linecap="round" stroke-linejoin="round">
    <path d="M40 72 C21 72 11 57 18 43 C23 33 35 30 43 34 C45 15 66 8 79 21 C85 26 88 34 86 41 C104 39 116 53 109 66 C106 71 101 72 96 72 Z"/>
    <path d="M52 54 a8 8 0 0 1 15 0"/>
    <path d="M73 54 a8 8 0 0 1 15 0"/>
    <path d="M44 72 L40 87"/><circle cx="38" cy="97" r="8.5"/>
    <path d="M65 72 L61 93"/><circle cx="59" cy="103" r="8.5"/>
    <path d="M87 72 L91 89"/><circle cx="93" cy="99" r="8.5"/>
</svg>
