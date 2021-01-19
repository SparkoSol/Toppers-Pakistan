<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use App\Product;

class MailSenderApi extends Mailable
{
    use Queueable, SerializesModels;

    public $data;
    public $subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data,$subject)
    {
        $this->data = $data;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $product = array();
        for ($i = 0; $i < count($this->data['items']); $i++){
            $product[$i] = Product::where('id',$this->data['items'][$i]['product_id'])->first();
        }
        return $this
            ->from('info@apnapos.pk','Apna Store')
            ->subject($this->subject)
            ->view('order-placed-mail-api-template',compact('product'))
            ->with('data',$this->data);
    }
}
