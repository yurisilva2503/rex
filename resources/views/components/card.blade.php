@props([
    'color' => null,
    'title' => null,
    'footer' => null,
    'class' => null
])

<div class="card w-100 {{ isset($class) ? $class : '' }}">
    @if(isset($title))
        <div class="card-header {{ $color ? 'bg-' . $color . ' text-white' : '' }}">
            {!! $title !!}
        </div>
    @endif

    <div class="card-body">
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endif
</div>
