<x-mail::message>
# New Inquiry

Star Melee

@component('mail::table')
    | Name     | Email    | Mobile   |
    | --------:| --------:| --------:|
    | {{$details['data']['name']}} | {{$details['data']['email']}} | {{$details['data']['mobile']}}

    | Shape         | Color         | Clarity  | Carat    | Price    | Quantity | Total    |
    | ------------- |:-------------:| --------:| --------:| --------:| --------:| --------:|
    | {{$details['data']['shape']}} | {{$details['data']['color']}} | {{$details['data']['clarity']}} | {{$details['data']['carat']}} | {{$details['data']['price']}} | {{$details['data']['qty']}} | {{$details['data']['price'] * $details['data']['qty']}} |
@endcomponent

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
