<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Queue\ShouldQueue;

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
        $discountAmount = number_format($this->order->discount_percent, 2); // Assuming discount_percent is the correct amount field
        $finalAmount = number_format($this->order->final_price, 2); // Assuming final_price is the correct amount field
        $transactionId = $this->transaction->transaction_id;
        $status = $this->transaction->status;

        // Get payment method dynamically
        $paymentMethod = ucfirst($this->transaction->payment_method); // Capitalize payment method for display (e.g., "kpay" => "Kpay")

        // Get the image URL for the payment screenshot
        $imagePath = optional($this->transaction->images()->first())->upload_url;

    if ($imagePath && Storage::exists('public/' . $imagePath)) {
        // Get the file content and encode it to base64
        $imageContent = Storage::get('public/' . $imagePath);
        $base64Image = base64_encode($imageContent);

        // Generate the image tag using base64 encoding
        $imageTag = "<p><strong>Payment Screenshot:</strong><br><img src='data:image/jpeg;base64,{$base64Image}' style='max-width: 300px;'></p>";
    }
        // Construct the email message
        $message = "
            <h2>Payment Successful for Order #{$this->order->id}</h2>
            <p>Dear Shop Owner,</p>
            <p>A payment has been successfully processed for Order #{$this->order->id}.</p>
            <h3>Order Details:</h3>
            <ul>
                <li><strong>Order ID:</strong> {$this->order->id}</li>
                <li><strong>Total Amount:</strong> \${$totalAmount}</li>
                <li><strong>Discount Amount:</strong> \${$discountAmount}</li>
                <li><strong>Final Amount:</strong> \${$finalAmount}</li>
                <li><strong>Payment Method:</strong> {$paymentMethod}</li>
                <li><strong>Transaction ID:</strong> {$transactionId}</li>
                <li><strong>Status:</strong> {$status}</li>
            </ul>
            {$imageTag} <!-- Display the image if it exists -->
            <p>Thank you for using our service.</p>
        ";

        // Send the email with the above details
        return $this->subject('Payment Successful for Order #' . $this->order->id)
                    ->html($message);
    }
}
