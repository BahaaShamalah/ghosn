@component('mail::message')
# {{ $donation->isPaid() ? 'Thank you for your donation' : 'Your donation is registered' }}

@if ($donation->isPaid())
Your generous gift of **{{ $donation->formattedAmount() }}** has been received. Reference: **{{ $donation->reference }}**.
@elseif ($donation->payment_method === 'bank_transfer')
Thank you for choosing to support GHOSN Relief Team.

Please complete your bank transfer of **{{ $donation->formattedAmount() }}** and include this reference: **{{ $donation->reference }}**.

Our team will confirm your donation once the transfer is received.
@else
We received your donation request for **{{ $donation->formattedAmount() }}**. Reference: **{{ $donation->reference }}**.
@endif

@if ($donation->message)
**Your message:**  
{{ $donation->message }}
@endif

@component('mail::button', ['url' => route('home')])
Visit GHOSN Relief
@endcomponent

With gratitude,<br>
{{ config('app.name') }}
@endcomponent
