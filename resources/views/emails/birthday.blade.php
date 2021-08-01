@component('mail::message')
   @component('mail::panel')
    <div class="row">
        <div style="background-image: url('https://www.appnovasolutions.com/zeus_api/public/happyBirthday.jpg');min-height: 300px;background-repeat: no-repeat;background-size: contain;">
            <div style="margin:5px;padding: 5px;color: white;font-size: xx-large;font-style: italic;">Feliz cumpleaños </div>
            <div style="margin:5px;padding: 5px;color: white;font-size: x-large;font-style: italic;"> {{ $user->name }} </div>
        </div>
        *Te desea JC Riesgos* (prueba)
    </div>
   @endcomponent
@endcomponent