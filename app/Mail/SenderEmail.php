<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SenderEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $form_data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($form_data)
    {
        $this->form_data = $form_data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->form_data['subject'])
        ->view('emails.template')->with(array(
            'body' => $this->form_data['body']
        ));
    }
}
