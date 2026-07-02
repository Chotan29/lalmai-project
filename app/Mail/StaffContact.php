<?php

namespace App\Mail;

use App\Models\EmailSetting;
use App\Models\GeneralSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class StaffContact extends Mailable
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

        return $this->from($general_setting->email, $general_setting->site_title)
            ->subject('Contact Message From '.$general_setting->site_title)
            ->view('mail.staffContact',[
                'data' => $this->data,
                'generalSetting' => $general_setting
            ]);
    }
}
