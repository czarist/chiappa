# Chiappa CMS

CMS Laravel multipágina para o site institucional da Chiappa Auto Peças e Serviços. O frontend é Blade componentizado e preserva a estrutura dos três templates fornecidos. As variações escura, cinza e clara são presets do mesmo layout.

## Instalação com Docker

```bash
cd site
cp .env.example .env
docker compose up -d --build
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed --force
docker compose exec app php artisan storage:link
```

Abra `http://localhost:8080`. O administrador inicial usa `ADMIN_EMAIL` e `ADMIN_PASSWORD` do `.env`; por padrão são `admin@example.com` e `change-me`. Altere-os antes de produção.

## Desenvolvimento local

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

Para executar sem Docker, altere `DB_CONNECTION` para `sqlite` e crie `database/database.sqlite`.

## Arquitetura

- `app/Models`: Page, PageSection, SEO, Setting, Theme, Media, Menu e usuários.
- `app/Services`: regras de negócio de páginas e configurações/cache.
- `app/Http/Controllers/Admin`: painel protegido por autenticação e middleware `admin`.
- `resources/views/components/site`: header, footer e blocos públicos reutilizáveis.
- `resources/views/admin`: páginas, seções, mídia, menu, temas, configurações e usuários.
- `database/seeders/DatabaseSeeder.php`: conteúdo inicial extraído do template.

## Conteúdo e temas

Em `/admin/pages`, cada página possui uma coleção ordenada de seções. O editor usa JSON estruturado, sem armazenar HTML bruto. Tipos semeados: `hero`, `differentials`, `services`, `products`, `instagram`, `stats`, `cta` e `contact`. Para adicionar um tipo, crie um Blade em `resources/views/components/site/sections` e inclua o nome no `PageController`.

As três versões originais compartilham o mesmo HTML e são representadas pelos presets `default`, `gray` e `light` em `/admin/themes`.

## API

Base `/api/v1`:

- `GET /pages` e `GET /pages/{page}`
- `GET /settings`
- `GET /menus`
- `GET /media`
- `POST`, `PUT` e `DELETE /pages` exigem sessão autenticada de administrador.

As respostas seguem `{ success, data, message }`.

## SEO e testes

Cada página possui SEO title, descrição, canonical, robots, Open Graph e Twitter card. O sistema expõe `/sitemap.xml` e `/robots.txt`.

```bash
php artisan test
```

O ambiente Docker persiste MySQL em `mysql_data` e uploads em `storage_data`.
