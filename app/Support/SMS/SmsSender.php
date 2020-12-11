<?php

namespace App\Support\SMS;

interface SmsSender
{
    function isConfigured(): bool;

    function sendMessage(string $message, string $recipient);
}
