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
        assert(in_array($type, ['tel', 'sms']), '$type must be one of [tel, sms]');

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
