<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Nuevo comprobante de pago</title>
</head>
<body style="margin:0; padding:0; background-color:#faf9f5; font-family:-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#faf9f5; padding:24px 12px;">
<tr>
<td align="center">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:480px; background-color:#ffffff; border-radius:12px; overflow:hidden; border:1px solid #e7e5e4;">

<tr>
<td style="background-color:#0f1629; padding:20px 28px;">
<span style="color:#f8fafc; font-size:18px; font-weight:600;">🛍️ {{ $storeName }}</span>
</td>
</tr>

<tr>
<td style="padding:28px;">
<p style="margin:0 0 4px; font-size:12px; font-weight:600; letter-spacing:0.05em; text-transform:uppercase; color:#c9961f;">💰 Comprobante recibido</p>
<h1 style="margin:0 0 20px; font-size:20px; color:#0f1629;">Pedido #{{ $order->id }}</h1>

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#faf9f5; border-radius:8px; margin-bottom:20px;">
<tr>
<td style="padding:16px 18px;">
<p style="margin:0 0 2px; font-size:11px; color:#a8a29e; text-transform:uppercase; letter-spacing:0.05em;">Cliente</p>
<p style="margin:0 0 14px; font-size:15px; color:#0f1629; font-weight:600;">{{ $order->customer_name }} · {{ $order->customer_phone }}</p>

<p style="margin:0 0 2px; font-size:11px; color:#a8a29e; text-transform:uppercase; letter-spacing:0.05em;">Prendas</p>
<p style="margin:0 0 14px; font-size:14px; color:#44403c; line-height:1.6;">
@foreach($order->items as $item){{ $item->variant->product->name }} ({{ $item->variant->color }}/{{ $item->variant->size }}) x{{ $item->quantity }}@if(!$loop->last)<br>@endif @endforeach
</p>

<p style="margin:0 0 2px; font-size:11px; color:#a8a29e; text-transform:uppercase; letter-spacing:0.05em;">Total</p>
<p style="margin:0; font-size:22px; color:#0f1629; font-weight:700;">{{ $currencySymbol }}{{ store_money($order->total) }}</p>
</td>
</tr>
</table>

<table role="presentation" cellpadding="0" cellspacing="0">
<tr>
<td style="border-radius:8px; background-color:#0f1629;">
<a href="{{ $panelUrl }}" style="display:inline-block; padding:12px 24px; font-size:14px; font-weight:600; color:#ffffff; text-decoration:none;">Ver y confirmar pago →</a>
</td>
</tr>
</table>
</td>
</tr>

<tr>
<td style="padding:16px 28px; background-color:#faf9f5; border-top:1px solid #e7e5e4;">
<p style="margin:0; font-size:12px; color:#a8a29e;">Recuerda revisar la foto del comprobante en el panel antes de marcar el pedido como pagado.</p>
</td>
</tr>

</table>
</td>
</tr>
</table>
</body>
</html>
