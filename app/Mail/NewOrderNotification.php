<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewOrderNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
        $this->order->loadMissing('items.variant.product');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "🛍️ Nuevo comprobante · Pedido #{$this->order->id} · {$this->order->customer_name}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-order-notification',
            with: [
                'order' => $this->order,
                'panelUrl' => route('admin.orders.index'),
                'storeName' => config('store.name'),
                'currencySymbol' => config('store.currency_symbol'),
            ],
        );
    }
}
