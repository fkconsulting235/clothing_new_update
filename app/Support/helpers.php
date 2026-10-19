<?php

if (! function_exists('store_money')) {
    /**
     * Formatea un número como 1,234.56 (punto decimal, coma de miles),
     * sin importar la localización activa — igual que el dólar.
     */
    function store_money(mixed $value): string
    {
        return number_format((float) $value, 2, '.', ',');
    }
}
