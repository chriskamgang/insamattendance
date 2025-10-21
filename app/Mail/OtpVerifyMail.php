<?php

namespace App\Mail;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
class OtpVerifyMail extends Mailable
{
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $mailData;

    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'OTP Verify Mail',
            tags: ['verification'],
            metadata: [
                'user_id' => $this->mailData->id,
            ],
        );
    }

    public function build(): OtpVerifyMail
    {
        return
            $this->markdown('notification.otp_verify')
                ->with([
                    'name' => $this->mailData->name,
                    'otp_code' => $this->mailData->two_factor_code
                ]);
    }



}
