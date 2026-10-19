<?php

namespace App\Services;

use App\Models\ProductVariant;
use Illuminate\Support\Facades\Session;

class Cart
{
    protected const SESSION_KEY = 'cart';

    protected function raw(): array
    {
        return Session::get(self::SESSION_KEY, []);
    }

    protected function save(array $data): void
    {
        Session::put(self::SESSION_KEY, $data);
    }

    public function add(int $variantId, int $quantity): void
    {
        $cart = $this->raw();
        $cart[$variantId] = ($cart[$variantId] ?? 0) + $quantity;
        $this->save($cart);
    }

    public function update(int $variantId, int $quantity): void
    {
        $cart = $this->raw();
        if ($quantity > 0) {
            $cart[$variantId] = $quantity;
        } else {
            unset($cart[$variantId]);
        }
        $this->save($cart);
    }

    public function remove(int $variantId): void
    {
        $cart = $this->raw();
        unset($cart[$variantId]);
        $this->save($cart);
    }

    public function clear(): void
    {
        Session::forget(self::SESSION_KEY);
    }

    public function lines(): array
    {
        $cart = $this->raw();
        if (empty($cart)) {
            return [];
        }

        $variants = ProductVariant::with('product')->whereIn('id', array_keys($cart))->get()->keyBy('id');

        $lines = [];
        foreach ($cart as $variantId => $quantity) {
            $variant = $variants->get($variantId);
            if (! $variant) {
                continue;
            }
            $lines[] = [
                'variant' => $variant,
                'quantity' => $quantity,
                'subtotal' => bcmul((string) $variant->price, (string) $quantity, 2),
            ];
        }

        return $lines;
    }

    public function total(): string
    {
        $total = '0.00';
        foreach ($this->lines() as $line) {
            $total = bcadd($total, $line['subtotal'], 2);
        }

        return $total;
    }

    public function count(): int
    {
        return array_sum($this->raw());
    }
}
