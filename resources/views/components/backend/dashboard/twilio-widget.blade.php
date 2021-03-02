@isset($twilioBalance)
    <div class="col">
        <x-card>
            <x-slot name="title">
                <a
                    href="https://www.twilio.com/console/billing"
                    class="text-body text-decoration-none"
                    target="_blank">
                    Twilio Account
                </a>
            </x-slot>
            <span class="@if($twilioBalance->balance < 10) text-danger @endif">
                {{ round($twilioBalance->balance, 2) }} {{ $twilioBalance->currency }} balance
            </span>
        </x-card>
    </div>
@endisset