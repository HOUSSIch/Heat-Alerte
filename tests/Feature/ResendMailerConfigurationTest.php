<?php

namespace Tests\Feature;

use Illuminate\Mail\Transport\ResendTransport;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

class ResendMailerConfigurationTest extends TestCase
{
    public function test_resend_mailer_can_be_resolved_without_sending_an_external_email(): void
    {
        config()->set('mail.default', 'resend');
        config()->set('mail.mailers.resend', ['transport' => 'resend']);
        config()->set('services.resend.key', Str::random(48));
        app('mail.manager')->forgetMailers();

        $mailer = Mail::mailer();

        $this->assertInstanceOf(ResendTransport::class, $mailer->getSymfonyTransport());
    }
}
