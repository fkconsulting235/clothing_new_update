<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('admin:create {email? : Correo del administrador (o ADMIN_EMAIL del .env)} {password? : Contraseña (o ADMIN_PASSWORD del .env)} {--name= : Nombre visible}')]
#[Description('Crea (o actualiza la contraseña de) un usuario administrador del panel')]
class CreateAdmin extends Command
{
    public function handle(): int
    {
        $email = $this->argument('email') ?: env('ADMIN_EMAIL');
        $password = $this->argument('password') ?: env('ADMIN_PASSWORD');

        if (! $email || ! $password) {
            $this->error('Falta el correo o la contraseña. Pásalos como argumentos o define ADMIN_EMAIL y ADMIN_PASSWORD en el .env.');

            return self::FAILURE;
        }

        if (strlen($password) < 8) {
            $this->error('La contraseña debe tener al menos 8 caracteres.');

            return self::FAILURE;
        }

        $user = User::firstOrNew(['email' => $email]);
        $existed = $user->exists;
        $user->name = $this->option('name') ?: ($user->name ?: 'Administrador');
        $user->password = $password;
        $user->is_staff = true;
        $user->save();

        $this->info(($existed ? 'Administrador actualizado: ' : 'Administrador creado: ').$email);

        return self::SUCCESS;
    }
}
