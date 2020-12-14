<?php

namespace App\View\Components;

use Illuminate\View\Component;

class PhoneNumberLink extends Component
{
    public string $value;
    public string $type;
    public ?string $body;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(string $value, string $type = 'tel', ?string $body = null)
    {
        $types = ['tel', 'sms', 'whatsapp', 'viber'];
        assert(in_array($type, $types), '$type must be one of [' . implode(', ', $types) . ']');

        $this->value = $value;
        $this->type = $type;
        $this->body = $body;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|string
     */
    public function render()
    {
        return view('components.phone-number-link');
    }

    public function href()
    {
        if ($this->type == 'whatsapp') {
            $user_agent = request()->userAgent();
            $iphone = strpos($user_agent, 'iPhone');
            $android = strpos($user_agent, 'Android');
            $palmpre = strpos($user_agent, 'webOS');
            $berry = strpos($user_agent, 'BlackBerry');
            $ipod = strpos($user_agent, 'iPod');
            $chrome = strpos($user_agent, 'Chrome');
            $value = '';
            if ($android || $iphone) {
                $value = 'whatsapp://send?phone=';
            } elseif ($palmpre || $ipod || $berry || $chrome) {
                $value = 'https://api.whatsapp.com/send?phone=';
            } else {
                $this->attributes['target'] = '_blank';
                $value = 'https://web.whatsapp.com/send?phone=';
            }
            $value .= preg_replace('/[^0-9]/', '', $this->value);
            if (filled($this->body)) {
                $value .= '&text=' . urlencode($this->body);
            }
            return $value;
        }
        if ($this->type == 'viber') {
            $value = 'viber://chat/?number=' . urlencode($this->value);
            if (filled($this->body)) {
                $value .= '&text=' . urlencode($this->body);
            }
            return $value;
        }
        if ($this->type == 'sms') {
            $value = 'sms://' . $this->value;
            if (filled($this->body)) {
                $value .= '?body=' . urlencode($this->body);
            }
            return $value;
        }
        return 'tel:' . $this->value;
    }
}
