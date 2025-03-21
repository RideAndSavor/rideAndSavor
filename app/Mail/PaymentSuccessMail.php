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
        // Access the total amount correctly
        $totalAmount = number_format($this->order->total_price, 2); // Assuming total_price is the correct amount field
        $discountAmount = number_format($this->order->discount_price, 2); // Assuming discount_price is the correct amount field
        $finalAmount = number_format($this->order->final_price, 2); // Assuming final_price is the correct amount field
        $transactionId = $this->transaction->transaction_id;
        $status = $this->transaction->status;

        $message = "
            <h2>Payment Successful for Order #{$this->order->id}</h2>
            <p>Dear Shop Owner,</p>
            <p>A payment has been successfully processed for Order #{$this->order->id}.</p>
            <h3>Order Details:</h3>
            <ul>
                <li><strong>Order ID:</strong> {$this->order->id}</li>
                <li><strong>Total Amount:</strong> \${$totalAmount}</li> <!-- Accessing total_price correctly -->
                <li><strong>Discount Amount:</strong> \${$discountAmount}</li> <!-- Accessing discount_price correctly -->
                <li><strong>Final Amount:</strong> \${$finalAmount}</li> <!-- Accessing final_price correctly -->
                <li><strong>Payment Method:</strong> Credit Card (Stripe)</li>
                <li><strong>Transaction ID:</strong> {$transactionId}</li>
                <li><strong>Status:</strong> {$status}</li>
            </ul>
            <p>Thank you for using our service.</p>
        ";

        return $this->subject('Payment Successful for Order #' . $this->order->id)
                    ->html($message);
    }

}
