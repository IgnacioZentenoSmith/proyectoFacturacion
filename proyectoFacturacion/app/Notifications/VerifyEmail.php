<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Carbon\Carbon;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class VerifyEmail extends VerifyEmailBase
{
//    use Queueable;

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
      VerifyEmail::toMailUsing(function ($notifiable) {
        $verifyUrl = URL::temporarySignedRoute(
            'verification.verify', Carbon::now()->addMinutes(60), ['id' => $notifiable->getKey()]
        );

        return (new MailMessage)
            ->subject('Email de confirmación de correo, equipo de facturaciones PlanOK.')
            ->markdown('emails.emailVerification', ['url' => $verifyUrl]);
        });
    }
}