<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class SendOrderPDFMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;
    protected $qty;
    protected $emails;
    protected $path;

    /**
     * Create a new job instance.
     */
    public function __construct($order, $qty, $emails, $path)
    {
        $this->order = $order;
        $this->qty = $qty;
        $this->emails = $emails;
        $this->path = $path;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $attachmentName = "Order_{$this->order->id}.pdf";

        Mail::send('emails.order_confirmation', ['order' => $this->order, 'qty' => $this->qty], function ($message) use ($attachmentName) {
            $message->to($this->emails)
                ->subject("Order Confirmation: #{$this->order->id}")
                ->attach(storage_path("app/{$this->path}"), ['as' => $attachmentName, 'mime' => 'application/pdf']);
        });

        Storage::delete($this->path);
    }
}
