<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

#[Signature('store:install')]
#[Description('Deja la tienda lista en un servidor nuevo: tablas, enlace de fotos y administrador')]
class StoreInstall extends Command
{
    public function handle(): int
    {
        $this->line('1/3 Creando tablas...');
        $this->call('migrate', ['--force' => true]);

        $this->line('2/3 Enlazando la carpeta de fotos...');
        $link = public_path('storage');
        if (File::exists($link) || is_link($link)) {
            $this->line('  ya existe, se deja como está.');
        } else {
            $this->call('storage:link');
        }

        $this->line('3/3 Administrador del panel...');
        if (env('ADMIN_EMAIL') && env('ADMIN_PASSWORD')) {
            $this->call('admin:create');
        } else {
            $this->warn('  ADMIN_EMAIL / ADMIN_PASSWORD no están en el .env: no se creó administrador.');
        }

        $this->call('config:clear');
        $this->info('Instalación terminada.');

        return self::SUCCESS;
    }
}
