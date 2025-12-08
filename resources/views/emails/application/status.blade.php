<x-mail::message>
    # Hello {{ $application->clientProfile->user->name }},

    We have an update regarding your application to **{{ $application->university_name }}**.

    <x-mail::panel>
        **Current Status:** {{ ucwords(str_replace('_', ' ', $status)) }}
    </x-mail::panel>

    @if($status === 'approved')
        Congratulations! Your academic application has been accepted. Our **Visa Consultant** will now review your file for the next stage.
    @elseif($status === 'rejected' || $status === 'visa_rejected')
        **Reason for Return:**
        > "{{ $application->notes }}"

        Please log in to your dashboard to edit your application and resubmit it with the necessary changes.
    @elseif($status === 'visa_granted')
        Your Visa has been approved! Please log in to submit your travel preferences so we can book your flight.
    @elseif($status === 'travel_booked')
        Your tickets have been booked! You can download them from your dashboard now.
    @else
        Your application status has changed. Please log in to view the latest details.
    @endif

    <x-mail::button :url="route('applications.show', $application->id)">
        View Dashboard
    </x-mail::button>

    Thanks,
    {{ config('app.name') }} Team
</x-mail::message>
