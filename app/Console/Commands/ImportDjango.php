<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

#[Signature('import:django
    {--driver=sqlite : sqlite o mysql}
    {--database= : Ruta del archivo sqlite, o nombre de la base MySQL}
    {--host=localhost : Servidor MySQL}
    {--port=3306 : Puerto MySQL}
    {--username= : Usuario MySQL}
    {--password= : Contraseña MySQL}
    {--media= : Carpeta media/ del proyecto Django (para copiar las fotos)}
    {--fresh : Borra primero los datos actuales de esta tienda}
    {--force : No pedir confirmación}')]
#[Description('Importa categorías, prendas, fotos, cuentas, reservas y pedidos desde la base del proyecto Django anterior')]
class ImportDjango extends Command
{
    public function handle(): int
    {
        $driver = $this->option('driver');
        $database = $this->option('database');

        if (! $database) {
            $this->error('Falta --database (ruta del .sqlite o nombre de la base MySQL).');

            return self::FAILURE;
        }

        config(['database.connections.legacy' => $driver === 'mysql' ? [
            'driver' => 'mysql',
            'host' => $this->option('host'),
            'port' => $this->option('port'),
            'database' => $database,
            'username' => $this->option('username'),
            'password' => $this->option('password'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ] : [
            'driver' => 'sqlite',
            'database' => $database,
            'prefix' => '',
            'foreign_key_constraints' => false,
        ]]);

        $legacy = DB::connection('legacy');

        try {
            $legacy->table('catalog_category')->count();
        } catch (\Throwable $e) {
            $this->error('No se pudo leer la base del Django: '.$e->getMessage());

            return self::FAILURE;
        }

        if ($this->option('fresh')) {
            if (! $this->option('force') && ! $this->confirm('Esto BORRA las prendas, pedidos y reservas actuales de esta tienda. ¿Continuar?')) {
                return self::FAILURE;
            }
            $this->wipe();
        } elseif (DB::table('products')->exists() || DB::table('categories')->exists()) {
            $this->error('Esta tienda ya tiene datos. Usa --fresh para reemplazarlos por los del Django.');

            return self::FAILURE;
        }

        DB::transaction(function () use ($legacy) {
            $this->copy($legacy, 'catalog_category', 'categories', fn ($r) => [
                'id' => $r->id, 'name' => $r->name, 'slug' => $r->slug, 'order' => $r->order,
                'is_active' => (bool) $r->is_active,
            ]);

            $this->copy($legacy, 'catalog_product', 'products', fn ($r) => [
                'id' => $r->id, 'category_id' => $r->category_id, 'name' => $r->name, 'slug' => $r->slug,
                'code' => $r->code ?: null, 'description' => $r->description ?: null,
                'base_price' => $r->base_price, 'is_active' => (bool) $r->is_active,
                'created_at' => $this->date($r->created_at), 'updated_at' => $this->date($r->created_at),
            ]);

            $this->copy($legacy, 'catalog_productvariant', 'product_variants', fn ($r) => [
                'id' => $r->id, 'product_id' => $r->product_id, 'color' => $r->color, 'size' => $r->size,
                'sku' => $r->sku ?: null, 'price_override' => $r->price_override, 'stock' => $r->stock,
                'created_at' => now(), 'updated_at' => now(),
            ]);

            $this->copy($legacy, 'catalog_productimage', 'product_images', fn ($r) => [
                'id' => $r->id, 'product_id' => $r->product_id, 'image_path' => $r->image, 'order' => $r->order,
                'created_at' => now(), 'updated_at' => now(),
            ]);

            $this->copy($legacy, 'orders_bankaccount', 'bank_accounts', fn ($r) => [
                'id' => $r->id, 'bank_name' => $r->bank_name, 'account_type' => $r->account_type ?: null,
                'account_number' => $r->account_number, 'holder_name' => $r->holder_name,
                'order' => $r->order, 'is_active' => (bool) $r->is_active,
                'created_at' => now(), 'updated_at' => now(),
            ]);

            $this->copy($legacy, 'reservations_reservation', 'reservations', fn ($r) => [
                'id' => $r->id, 'variant_id' => $r->variant_id, 'customer_name' => $r->customer_name,
                'customer_phone' => $r->customer_phone, 'quantity' => $r->quantity, 'status' => $r->status,
                'expires_at' => $this->date($r->expires_at),
                'created_at' => $this->date($r->created_at), 'updated_at' => $this->date($r->created_at),
            ]);

            $this->copy($legacy, 'orders_order', 'orders', fn ($r) => [
                'id' => $r->id, 'customer_name' => $r->customer_name, 'customer_phone' => $r->customer_phone,
                'customer_email' => $r->customer_email ?: null, 'status' => $r->status,
                'payment_proof_path' => $r->payment_proof ?: null, 'total' => $r->total,
                'reservation_id' => $r->reservation_id,
                'created_at' => $this->date($r->created_at), 'updated_at' => $this->date($r->created_at),
            ]);

            $this->copy($legacy, 'orders_orderitem', 'order_items', fn ($r) => [
                'id' => $r->id, 'order_id' => $r->order_id, 'variant_id' => $r->variant_id,
                'quantity' => $r->quantity, 'unit_price' => $r->unit_price,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        });

        $this->resetSequences();
        $this->copyMedia($legacy);

        $this->newLine();
        $this->info('Listo. Los usuarios del panel NO se importan (las contraseñas de Django no sirven en Laravel): crea un administrador nuevo.');

        return self::SUCCESS;
    }

    protected function copy($legacy, string $from, string $to, \Closure $map): void
    {
        $count = 0;
        foreach ($legacy->table($from)->orderBy('id')->get() as $row) {
            DB::table($to)->insert($map($row));
            $count++;
        }
        $this->line(sprintf('  %-22s %d registros', $to, $count));
    }

    protected function date($value): ?string
    {
        return $value ? Carbon::parse($value, 'UTC')->format('Y-m-d H:i:s') : null;
    }

    protected function wipe(): void
    {
        Schema_disableFk();
        foreach (['order_items', 'orders', 'reservations', 'product_images', 'product_variants', 'products', 'categories', 'bank_accounts'] as $table) {
            DB::table($table)->delete();
        }
        Schema_enableFk();
    }

    protected function resetSequences(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }
        foreach (['categories', 'products', 'product_variants', 'product_images', 'bank_accounts', 'reservations', 'orders', 'order_items'] as $table) {
            $next = (int) DB::table($table)->max('id') + 1;
            DB::statement("ALTER TABLE {$table} AUTO_INCREMENT = {$next}");
        }
    }

    protected function copyMedia($legacy): void
    {
        $media = $this->option('media');
        if (! $media) {
            $this->warn('Sin --media: las fotos NO se copiaron (solo los datos).');

            return;
        }

        $paths = array_merge(
            DB::table('product_images')->pluck('image_path')->all(),
            DB::table('orders')->whereNotNull('payment_proof_path')->pluck('payment_proof_path')->all(),
        );

        $copied = $missing = 0;
        foreach ($paths as $path) {
            $source = rtrim($media, '/\\').DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $path);
            $target = storage_path('app/public/'.$path);
            if (! File::exists($source)) {
                $missing++;

                continue;
            }
            File::ensureDirectoryExists(dirname($target));
            File::copy($source, $target);
            $copied++;
        }

        $this->line("  fotos copiadas: {$copied}".($missing ? " (no se encontraron: {$missing})" : ''));
    }
}

function Schema_disableFk(): void
{
    match (DB::getDriverName()) {
        'sqlite' => DB::statement('PRAGMA foreign_keys = OFF'),
        'mysql' => DB::statement('SET FOREIGN_KEY_CHECKS = 0'),
        default => null,
    };
}

function Schema_enableFk(): void
{
    match (DB::getDriverName()) {
        'sqlite' => DB::statement('PRAGMA foreign_keys = ON'),
        'mysql' => DB::statement('SET FOREIGN_KEY_CHECKS = 1'),
        default => null,
    };
}
