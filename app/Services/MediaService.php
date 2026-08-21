<?php

namespace App\Services;

use App\Models\Media;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class MediaService
{
    private const DIRECTORY = 'assets/uploads';

    private const MAX_BYTES = 5_242_880;

    private const MIME_EXTENSIONS = [
        'image/jpeg' => 'jpg',
        'image/jpg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/gif' => 'gif',
        'image/svg+xml' => 'svg',
        'image/avif' => 'avif',
    ];

    public function storeUpload(UploadedFile $file, ?User $user = null, ?string $altText = null): Media
    {
        $originalName = $file->getClientOriginalName();
        $mime = $file->getMimeType() ?: 'image/jpeg';
        $extension = strtolower($file->getClientOriginalExtension() ?: $this->extensionFromMime($mime));
        $filename = Str::uuid()->toString().'.'.$extension;
        $relative = self::DIRECTORY.'/'.$filename;

        $absolute = $this->absolutePath($relative);
        if (file_put_contents($absolute, $file->get()) === false) {
            throw new RuntimeException('Não foi possível salvar a imagem.');
        }

        return Media::create([
            'user_id' => $user?->id,
            'name' => $originalName ?: $filename,
            'path' => $relative,
            'mime_type' => $mime,
            'size' => is_file($absolute) ? filesize($absolute) : 0,
            'alt_text' => $altText,
        ]);
    }

    public function storeFromUrl(string $url, ?User $user = null, ?string $altText = null): Media
    {
        $url = trim($url);
        $this->assertPublicUrl($url);

        $existingPath = self::DIRECTORY.'/img-'.sha1($url);
        $existing = Media::query()->where('path', 'like', $existingPath.'.%')->first();
        if ($existing && is_file($this->absolutePath($existing->path))) {
            return $existing;
        }

        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36',
            'Accept' => 'image/jpeg,image/png,image/webp,image/gif,image/avif,image/*,*/*;q=0.8',
            'Accept-Language' => 'pt-BR,pt;q=0.9',
        ])->timeout(25)->get($url);

        if (! $response->successful()) {
            throw new RuntimeException('Não foi possível baixar a imagem.');
        }

        $body = $response->body();
        if ($body === '' || strlen($body) > self::MAX_BYTES) {
            throw new RuntimeException('Imagem vazia ou maior que 5 MB.');
        }

        $mime = $this->detectMime($response->header('Content-Type'), $body);
        $extension = $this->extensionFromMime($mime);
        $filename = 'img-'.sha1($url).'.'.$extension;
        $relative = self::DIRECTORY.'/'.$filename;
        $absolute = $this->absolutePath($relative);

        if (! is_file($absolute)) {
            file_put_contents($absolute, $body);
        }

        return Media::query()->updateOrCreate(
            ['path' => $relative],
            [
                'user_id' => $user?->id,
                'name' => basename(parse_url($url, PHP_URL_PATH) ?: $filename),
                'mime_type' => $mime,
                'size' => filesize($absolute) ?: strlen($body),
                'alt_text' => $altText,
            ]
        );
    }

    public function localizeValue(mixed $value, ?User $user = null): mixed
    {
        if (! is_string($value) || ! $this->isRemoteImageUrl($value)) {
            return $value;
        }

        try {
            return $this->storeFromUrl($value, $user)->url();
        } catch (\Throwable $e) {
            report($e);

            return $value;
        }
    }

    public function localizeContent(mixed $value, ?User $user = null): mixed
    {
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = $this->localizeContent($item, $user);
            }

            return $value;
        }

        return $this->localizeValue($value, $user);
    }

    public function deleteFile(Media $media): void
    {
        $path = str_replace('\\', '/', (string) $media->path);
        if (str_starts_with($path, self::DIRECTORY.'/')) {
            $absolute = $this->absolutePath($path);
            if (is_file($absolute)) {
                unlink($absolute);
            }

            return;
        }

        $storagePath = public_path('storage/'.$path);
        if (is_file($storagePath)) {
            unlink($storagePath);
        }
    }

    public function isRemoteImageUrl(string $value): bool
    {
        if (! str_starts_with($value, 'http://') && ! str_starts_with($value, 'https://')) {
            return false;
        }

        $parts = parse_url($value);
        $host = strtolower((string) ($parts['host'] ?? ''));
        $path = strtolower((string) ($parts['path'] ?? ''));

        $appHost = strtolower((string) parse_url((string) config('app.url'), PHP_URL_HOST));
        if ($host === '' || $host === $appHost || in_array($host, ['localhost', '127.0.0.1', '::1', 'wa.me', 'api.whatsapp.com', 'www.google.com', 'maps.google.com'], true)) {
            return false;
        }

        if (preg_match('/\.(jpe?g|png|gif|webp|svg)(\?|$)/', $path) === 1) {
            return true;
        }

        foreach (['pexels.com', 'pixabay.com', 'unsplash.com', 'mlstatic.com', 'fbcdn.net', 'cdninstagram.com'] as $needle) {
            if (str_contains($host, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function directory(): string
    {
        $directory = public_path(self::DIRECTORY);
        if (! is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        return $directory;
    }

    private function absolutePath(string $relative): string
    {
        $this->directory();

        return public_path($relative);
    }

    private function assertPublicUrl(string $url): void
    {
        $parts = parse_url($url);
        $scheme = $parts['scheme'] ?? null;
        $host = strtolower((string) ($parts['host'] ?? ''));

        if (! in_array($scheme, ['http', 'https'], true) || $host === '') {
            throw new RuntimeException('URL inválida.');
        }

        if (in_array($host, ['localhost', '127.0.0.1', '::1'], true) || str_ends_with($host, '.local')) {
            throw new RuntimeException('URL local não permitida.');
        }

        if (filter_var($host, FILTER_VALIDATE_IP) && ! filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            throw new RuntimeException('URL privada não permitida.');
        }
    }

    private function detectMime(?string $header, string $body): string
    {
        $headerMime = strtolower(trim(explode(';', (string) $header)[0]));
        if (isset(self::MIME_EXTENSIONS[$headerMime])) {
            return $headerMime;
        }

        $detected = strtolower((string) (new \finfo(FILEINFO_MIME_TYPE))->buffer($body));
        if (isset(self::MIME_EXTENSIONS[$detected])) {
            return $detected;
        }

        throw new RuntimeException('O arquivo baixado não é uma imagem válida.');
    }

    private function extensionFromMime(string $mime): string
    {
        $mime = strtolower(trim(explode(';', $mime)[0]));

        return self::MIME_EXTENSIONS[$mime] ?? 'jpg';
    }
}
