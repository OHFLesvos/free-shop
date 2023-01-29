<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PhoneNumberLink extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public string $value,
        public string $type = 'tel',
        public ?string $body = null
    ) {
        $types = ['tel', 'sms', 'whatsapp', 'viber'];
        assert(in_array($type, $types), '$type must be one of [' . implode(', ', $types) . ']');
    }

    public function render(): View
    {
        return view('components.phone-number-link');
    }

    public function href(): string
    {
        if ($this->type == 'whatsapp') {
            return $this->formatWhatsApp();
        }
        if ($this->type == 'viber') {
            return $this->formatViber();
        }
        if ($this->type == 'sms') {
            return $this->formatSms();
        }

        return 'tel:' . $this->value;
    }

    private function formatWhatsApp(): string
    {
        $value = $this->whatsAppUrlByUserAgent(request()->userAgent());
        $value .= preg_replace('/[^0-9]/', '', $this->value);
        if (filled($this->body)) {
            $value .= '&text=' . urlencode($this->body);
        }

        return $value;
    }

    private function whatsAppUrlByUserAgent(string $userAgent): string
    {
        $iphone = strpos($userAgent, 'iPhone');
        $android = strpos($userAgent, 'Android');
        $palmpre = strpos($userAgent, 'webOS');
        $berry = strpos($userAgent, 'BlackBerry');
        $ipod = strpos($userAgent, 'iPod');
        $chrome = strpos($userAgent, 'Chrome');
        if ($android || $iphone) {
            return 'whatsapp://send?phone=';
        }
        if ($palmpre || $ipod || $berry || $chrome) {
            return 'https://api.whatsapp.com/send?phone=';
        }
        $this->attributes['target'] = '_blank';

        return 'https://web.whatsapp.com/send?phone=';
    }

    private function formatViber(): string
    {
        $value = 'viber://chat/?number=' . urlencode($this->value);
        if (filled($this->body)) {
            $value .= '&text=' . urlencode($this->body);
        }

        return $value;
    }

    private function formatSms(): string
    {
        $value = 'sms://' . $this->value;
        if (filled($this->body)) {
            $value .= '?body=' . urlencode($this->body);
        }

        return $value;
    }
}
