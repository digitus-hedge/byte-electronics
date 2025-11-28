<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderEnquiry extends Mailable
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
        // $this->logoUrl = url('assets/images/BYTE_LOGO.webp');
        // $this->checkMark = url('assets/images/Checkmark.png');
        $this->logoUrl = "https://placehold.co/115x78";
        $this->checkMark = "https://placehold.co/66x66";
    }

    public function build()
    {
        // Render the HTML with the variables
        $html = view('emails.order_enquiry', [
            'order' => $this->order,
            'billingAddress' => $this->billingAddress,
            'shippingAddress' => $this->shippingAddress,
            'cartItems' => $this->cartItems,
            'customer' => $this->customer,
            'logoUrl' => $this->logoUrl,
            'checkMark' => $this->checkMark,
        ])->render();

        return $this->subject('New Order Enquiry #' . $this->order->id)
                    ->html($html)
                    ->text('emails.order_enquiry_text');
    }
}