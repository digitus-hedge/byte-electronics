<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $billingAddress;
    public $shippingAddress;
    public $cartItems;
    public $customer;
    public $logoUrl;
    public $checkMark;
    
    public function __construct($order, $billingAddress, $shippingAddress, $cartItems, $customer)
    {
        $this->order = $order;
        $this->billingAddress = $billingAddress;
        $this->shippingAddress = $shippingAddress;
        $this->cartItems = $cartItems;
        $this->customer = $customer;
        $this->logoUrl = url('assets/images/BYTE_LOGO.webp');
        $this->checkMark = url('assets/images/Checkmark.png');
    }
    
    public function build()
    {
        // dd($this->order);
        $html = view('emails.order_confirmation', [
            'order' => $this->order,
            'billingAddress' => $this->billingAddress,
            'shippingAddress' => $this->shippingAddress,
            'cartItems' => $this->cartItems,
            'customer' => $this->customer,
            'logoUrl' => $this->logoUrl,
            'checkMark' => $this->checkMark,
        ])->render();
        return $this->subject('Order Confirmation #' . $this->order->id)
                    ->html($html)
                    ->text('emails.order_confirmation_text');
    }
}