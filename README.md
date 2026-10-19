# Tienda K E I

Tienda de ropa en Laravel 13: catálogo por categorías, búsqueda y filtro por talla, reservas de 24 h, carrito, pago por transferencia con comprobante, aviso por correo y panel de administración (`/panel`).

## Probar en tu computadora

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan admin:create tucorreo@ejemplo.com TuClave123
php artisan storage:link
php artisan serve
```

Tienda en `http://127.0.0.1:8000` y panel en `http://127.0.0.1:8000/panel/login`.

## Desplegar en cPanel (sin terminal)

Requisitos: PHP 8.3 o superior, y las extensiones `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `fileinfo` y `json` activas (cPanel → **MultiPHP Manager** / **Select PHP Version**).

1. **Código**: cPanel → **Git Version Control** → Create. Clona `https://github.com/fkconsulting235/clothing_new_update.git` en la carpeta `/home/TU_USUARIO/clothing_new_update` (la carpeta no debe existir o debe estar vacía).
2. **Dependencias**: en **File Manager** sube `vendor.zip` a esa carpeta y usa **Extract**. Debe quedar la carpeta `vendor/` junto a `artisan`.
3. **Subdominio**: cPanel → **Domains/Subdomains** → crea uno y en *Document Root* pon `clothing_new_update/public` (tiene que apuntar a `public`, no a la carpeta del proyecto). Actívale SSL (AutoSSL) y en **MultiPHP Manager** asígnale PHP 8.3.
4. **Base de datos**: **MySQL Databases** → crea una base y un usuario nuevos, y asígnale todos los privilegios.
5. **`.env`**: en File Manager, dentro de `clothing_new_update`, crea un archivo llamado `.env` y pega el contenido de `.env.production.example` con tus datos (base de datos, correo, `APP_URL`, `APP_KEY` y el administrador). La `APP_KEY` se genera con `php artisan key:generate --show` en tu computadora.
6. **Instalar**: **Cron Jobs** → agrega un cron de una sola ejecución (hora de aquí a 2 o 3 minutos, de hoy):

   ```
   cd /home/TU_USUARIO/clothing_new_update && /opt/cpanel/ea-php83/root/usr/bin/php artisan store:install
   ```

   Crea las tablas, enlaza la carpeta de fotos y crea el administrador. Borra el cron cuando termine.
7. Entra a `https://TU_SUBDOMINIO/panel/login`, y después borra del `.env` las líneas `ADMIN_EMAIL` y `ADMIN_PASSWORD`.

## Traer los datos del proyecto Django anterior

Con un cron de una sola ejecución (y borrándolo al terminar):

```
cd /home/TU_USUARIO/clothing_new_update && /opt/cpanel/ea-php83/root/usr/bin/php artisan import:django --driver=mysql --database=BASE_DEL_DJANGO --username=USUARIO --password=CLAVE --media=/home/TU_USUARIO/tiendaKEI/media --fresh --force
```

Trae categorías, prendas, variantes, fotos, cuentas bancarias, reservas y pedidos. Los usuarios del panel no se importan: se crea uno nuevo con `store:install` o `admin:create`.

## Actualizar después

**Git Version Control → Manage → Pull or Deploy**. Si una actualización agrega migraciones, ejecuta `php artisan migrate --force` con un cron de una sola vez.
