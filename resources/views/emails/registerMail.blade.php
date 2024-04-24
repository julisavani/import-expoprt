<x-mail::message>
# Confirm your email address to get started on Delight Diamond

Once you’ve confirmed that <b>{{ $details['email'] }}</b> is your email address, we’ll help you find your Delight Diamonds or create a new one.

<x-mail::button :url="$url">
Active Email
</x-mail::button>

If you haven’t requested this email, there’s nothing to worry about – you can safely ignore it.
Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
