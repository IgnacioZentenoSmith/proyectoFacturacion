<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailnotifQueue extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    protected $user;
    protected $data;
    protected $title;

    public function __construct($user, $data, $title)
    {
        $this->user = $user;
        $this->data = $data;
        $this->title = $title;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('facturacion@correo.planok.com', 'Facturaciones PlanOK Santiago')
            ->subject($this->title)
            ->markdown('emails.emailBinnacleNotification')
            ->with([
                'title' => $this->title,
                'data' => $this->data,
                'user' => $this->user,
            ]);
    }
}
