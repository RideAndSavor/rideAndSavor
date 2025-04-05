<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $status;
    public $remark;

    /**
     * Create a new message instance.
     *
     * @param $order
     * @param string $status
     * @param string|null $remark
     */
    public function __construct($order, $status, $remark = null)
    {
        $this->order = $order;
        $this->status = $status;
        $this->remark = $remark;
    }

    /**
     * Build the email message.
     *
     * @return $this
     */
    public function build()
    {
        $statusText = $this->status === 'confirmed' ? 'Confirmed' : 'Rejected';
        $orderId = $this->order->id;
        $finalAmount = number_format($this->order->final_price, 2);
        $orderItems = $this->order->orderDetails;

        // Inline HTML email content
        $message = "
            <html>
            <head>
                <title>Order Status</title>
            </head>
            <body style='font-family: Arial, sans-serif; line-height: 1.6;'>
                <h2 style='color: #333;'>Order #{$orderId} {$statusText}</h2>
                <p>Dear Customer,</p>
                <p>Your order #{$orderId} has been <strong>{$statusText}</strong>.</p>

                <h3>Order Details:</h3>
                <ul>
                    <li><strong>Order ID:</strong> {$orderId}</li>
                    <li><strong>Final Amount:</strong> \${$finalAmount}</li>
                    <li><strong>Status:</strong> {$statusText}</li>
                </ul>

                <h3>Products Ordered:</h3>
                <ul>";

        foreach ($orderItems as $item) {
            $message .= "<li><strong>{$item->product->name}</strong> - {$item->quantity} pcs</li>";
        }

        $message .= "</ul>";

        if ($this->status === 'rejected' && $this->remark) {
            $message .= "<h3>Rejection Reason:</h3><p>{$this->remark}</p>";
        }

        $message .= "<p>Thank you for shopping with us.</p></body></html>";

        return $this->subject("Order #{$orderId} - {$statusText}")
                    ->html($message);
    }
}
