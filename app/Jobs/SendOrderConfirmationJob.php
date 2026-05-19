<?php

namespace App\Jobs;

use App\Models\Order;
use App\Mail\OrderConfirmationMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;

class SendOrderConfirmationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Order $order;

    /**
     * Create a new job instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Generate the PDF
        $pdf = Pdf::loadView('pdf.invoice', ['order' => $this->order]);
        $pdfContent = $pdf->output();

        // Send the email with the PDF attached
        Mail::to($this->order->user->email)->send(
            new OrderConfirmationMail($this->order, $pdfContent)
        );
    }
}
