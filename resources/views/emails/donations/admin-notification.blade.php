@component('mail::message')
# New donation received

**Reference:** {{ $donation->reference }}  
**Amount:** {{ $donation->formattedAmount() }}  
**Method:** {{ $donation->payment_method }}  
**Status:** {{ $donation->status }}  
**Donor:** {{ $donation->displayDonorName() }}  
**Email:** {{ $donation->donor_email }}

@if ($donation->message)
**Message:**  
{{ $donation->message }}
@endif

@component('mail::button', ['url' => route('admin.donations.index')])
View donations
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
