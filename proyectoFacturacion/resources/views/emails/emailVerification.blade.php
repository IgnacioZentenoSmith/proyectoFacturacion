@component('mail::message')

Porfavor, confirme su email

@component('mail::button', ['url' => $url])
Confirmar email
@endcomponent
Su contraseña es su misma dirección de correo.

Este email de confirmación expirará en 60 minutos.

Saludos cordiales

@endcomponent