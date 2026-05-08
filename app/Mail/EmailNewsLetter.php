<?php

namespace App\Mail;

use App\Models\GeneralSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class EmailNewsLetter extends Mailable
{
    use Queueable, SerializesModels;
    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($array)
    {
        $this->data = $array;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $general_setting = GeneralSetting::select('institute', 'salogan','copyright', 'address','phone','email','website', 'email', 'logo',
           'facebook', 'twitter', 'linkedIn', 'youtube', 'instagram', 'whatsApp', 'skype', 'pinterest','wordpress')->first();

        return $this->from($general_setting->email, $general_setting->institute)
            ->subject($this->data['subject'])
            ->view('mail.alert',[
                'data' => $this->data,
                'generalSetting' => $general_setting
            ]);

    }
}
