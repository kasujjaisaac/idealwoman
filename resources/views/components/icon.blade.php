@props(['name' => 'community'])
<svg {{ $attributes->class(['icon']) }} viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
@switch($name)
@case('phone')<path d="M27 22v5a2 2 0 0 1-2 2C12 28 4 20 3 7a2 2 0 0 1 2-2h5l2 7-4 2c2 5 5 8 10 10l2-4 7 2Z"/>@break
@case('email')<rect x="3" y="6" width="26" height="20" rx="2"/><path d="m4 8 12 10L28 8"/>@break
@case('skills')<path d="m5 10 11-6 11 6-11 6zM5 10v12l11 6 11-6V10M16 16v12M10 7l11 6v7"/>@break
@case('education')<path d="M16 8C11 5 6 5 3 6v20c4-1 8-1 13 2 5-3 9-3 13-2V6c-3-1-8-1-13 2v20M7 11l5 1M7 16l5 1M20 12l5-1M20 17l5-1"/>@break
@case('health')<path d="M16 28 5 18C-3 10 9 0 16 9c7-9 19 1 11 9zM9 17h4l2-6 3 11 2-5h4"/>@break
@case('leadership')<path d="M16 28V5M16 6l11 3-11 5M7 28h18M7 21l-3 3 3 3M25 21l3 3-3 3"/>@break
@case('pin')<path d="M26 13c0 8-10 16-10 16S6 21 6 13a10 10 0 0 1 20 0Z"/><circle cx="16" cy="13" r="3"/>@break
@case('map')<path d="m3 7 8-4 10 4 8-4v22l-8 4-10-4-8 4ZM11 3v22M21 7v22"/>@break
@case('opportunity')<path d="M10 22c0-5-5-6-5-12a11 11 0 0 1 22 0c0 6-5 7-5 12M10 22h12M12 26h8M14 30h4M16 6v9M12 11l4 4 4-4"/>@break
@default<circle cx="16" cy="9" r="4"/><circle cx="5" cy="13" r="3"/><circle cx="27" cy="13" r="3"/><path d="M9 28v-5a7 7 0 0 1 14 0v5M1 27v-5a5 5 0 0 1 6-5M31 27v-5a5 5 0 0 0-6-5"/>
@endswitch
</svg>
