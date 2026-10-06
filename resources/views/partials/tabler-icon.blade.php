@php($iconClass = $class ?? 'icon icon-2')

@switch($name)
    @case('dashboard')
        <svg xmlns="http://www.w3.org/2000/svg" class="{{ $iconClass }}" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h6v6h-6z"/><path d="M14 4h6v6h-6z"/><path d="M4 14h6v6h-6z"/><path d="M14 14h6v6h-6z"/></svg>
        @break
    @case('sun')
        <svg xmlns="http://www.w3.org/2000/svg" class="{{ $iconClass }}" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v1m0 16v1m9-9h-1m-16 0h-1m15.364-6.364l-.707 .707m-11.314 11.314l-.707 .707m12.728 0l-.707-.707m-11.314-11.314l-.707-.707"/><path d="M12 7a5 5 0 1 0 0 10a5 5 0 0 0 0-10z"/></svg>
        @break
    @case('bolt')
        <svg xmlns="http://www.w3.org/2000/svg" class="{{ $iconClass }}" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 3v7h6l-8 11v-7h-6l8-11"/></svg>
        @break
    @case('map-pin')
        <svg xmlns="http://www.w3.org/2000/svg" class="{{ $iconClass }}" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 11a3 3 0 1 0 0-6a3 3 0 0 0 0 6z"/><path d="M12 22c4-4 7-7.582 7-12a7 7 0 1 0-14 0c0 4.418 3 8 7 12z"/></svg>
        @break
    @case('devices')
        <svg xmlns="http://www.w3.org/2000/svg" class="{{ $iconClass }}" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 20l7 0"/><path d="M13 16l8 0"/><path d="M13 12l8 0"/><path d="M3 4m0 2a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2h-4a2 2 0 0 1-2-2z"/></svg>
        @break
    @case('bulb')
        <svg xmlns="http://www.w3.org/2000/svg" class="{{ $iconClass }}" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12h1m8-9v1m8 8h1m-3.6-6.6l-.7 .7m-10.6 10.6l-.7 .7m0-11.3l.7 .7m10.6 10.6l.7 .7"/><path d="M9 16a5 5 0 1 1 6 0a3.5 3.5 0 0 0-1 3h-4a3.5 3.5 0 0 0-1-3"/><path d="M9.7 17l4.6 0"/></svg>
        @break
    @case('message')
        <svg xmlns="http://www.w3.org/2000/svg" class="{{ $iconClass }}" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 9h8"/><path d="M8 13h6"/><path d="M9 18h-5a2 2 0 0 1-2-2v-10a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v7"/><path d="M14 15v4a2 2 0 0 0 2 2h4l2-2v-4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2"/></svg>
        @break
    @case('user')
        <svg xmlns="http://www.w3.org/2000/svg" class="{{ $iconClass }}" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0-8 0"/><path d="M16 19h6"/><path d="M19 16v6"/><path d="M4 21v-2a4 4 0 0 1 4-4h4"/></svg>
        @break
    @case('logout')
        <svg xmlns="http://www.w3.org/2000/svg" class="{{ $iconClass }}" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 8v-3a1 1 0 0 0-1-1h-7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h7a1 1 0 0 0 1-1v-3"/><path d="M9 12h12l-3-3"/><path d="M18 15l3-3"/></svg>
        @break
    @case('thermometer')
        <svg xmlns="http://www.w3.org/2000/svg" class="{{ $iconClass }}" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13.5a4 4 0 1 0 4 0v-10a2 2 0 0 0-4 0v10.5"/><path d="M10 9h4"/></svg>
        @break
    @case('alert')
        <svg xmlns="http://www.w3.org/2000/svg" class="{{ $iconClass }}" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4"/><path d="M10.363 3.591l-8.106 13.502a1.914 1.914 0 0 0 1.641 2.907h16.204a1.914 1.914 0 0 0 1.641-2.907l-8.106-13.502a1.914 1.914 0 0 0-3.274 0z"/><path d="M12 16h.01"/></svg>
        @break
@endswitch

rrrrrrrrrrrrrrrrrrrrrrgqeeeee