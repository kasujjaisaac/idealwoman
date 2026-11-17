@props(['name' => 'grid'])
<svg {{ $attributes->class(['admin-icon']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.65" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
@switch($name)
@case('home')<path d="m3 10 9-7 9 7v11h-6v-7H9v7H3Z"/>@break
@case('file')<path d="M14 3H5v18h14V8Zm0 0v5h5M8 12h8M8 16h6"/>@break
@case('image')<rect x="3" y="3" width="18" height="18"/><circle cx="8" cy="8" r="1.5"/><path d="m3 17 5-5 4 4 4-6 5 7"/>@break
@case('mail')<rect x="3" y="5" width="18" height="14"/><path d="m3 6 9 7 9-7"/>@break
@case('users')<circle cx="9" cy="8" r="3"/><path d="M3 21v-3a6 6 0 0 1 12 0v3M16 5a3 3 0 0 1 0 6M21 21v-3a6 6 0 0 0-4-5"/>@break
@case('settings')<path d="M4 7h16M4 17h16M8 4v6M16 14v6"/>@break
@case('arrow')<path d="M4 12h16m-6-6 6 6-6 6"/>@break
@case('external')<path d="M14 3h7v7m0-7L10 14M10 3H3v18h18v-7"/>@break
@case('logout')<path d="M9 3H3v18h6M8 12h13m-5-5 5 5-5 5"/>@break
@case('clock')<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>@break
@case('shield')<path d="m12 3 8 3v6c0 5-8 9-8 9s-8-4-8-9V6Zm-4 9 3 3 5-6"/>@break
@case('check')<path d="m5 12 4 4L19 6"/>@break
@case('search')<circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5"/>@break
@case('plus')<path d="M12 4v16M4 12h16"/>@break
@case('chevron')<path d="m6 9 6 6 6-6"/>@break
@case('globe')<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c5 5 5 13 0 18-5-5-5-13 0-18Z"/>@break
@default<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>
@endswitch
</svg>
