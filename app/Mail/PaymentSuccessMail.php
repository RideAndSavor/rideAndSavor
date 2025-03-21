<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $transaction;

    public function __construct($order, $transaction)
    {
        $this->order = $order;
        $this->transaction = $transaction;
    }

    public function build()
    {
        $message = "
            <h2>Payment Successful for Order #{$this->order->id}</h2>
            <p>Dear Shop Owner,</p>
            <p>A payment has been successfully processed for Order #{$this->order->id}.</p>
            <h3>Order Details:</h3>
            <ul>
                <li><strong>Order ID:</strong> {$this->order->id}</li>
                <li><strong>Total Amount:</strong> \${{ number_format($this->order->total_amount, 2) }}</li>
                <li><strong>Payment Method:</strong> Credit Card (Stripe)</li>
                <li><strong>Transaction ID:</strong> {$this->transaction->transaction_id}</li>
                <li><strong>Status:</strong> {$this->transaction->status}</li>
            </ul>
            <p>Thank you for using our service.</p>
        ";

        return $this->subject('Payment Successful for Order #' . $this->order->id)
                    ->html($message);
    }
}
