<?php

namespace App\Console\Commands;

use App\Models\{Article, Asset, ContentPage, Setting};
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Storage, URL};
use RuntimeException;
use Throwable;

class ExportPublic extends Command
{
    protected $signature = 'app:export-public {--url=https://salfar256.github.io/ibnu-aqil-education-centre : Alamat HTTPS situs statis} {--output=pages : Nama direktori baru atau kosong di akar aplikasi} {--portal-url= : Alamat HTTPS portal PHP yang sudah aktif}';

    protected $description = 'Ekspor HTML publik terverifikasi untuk GitHub Pages; tidak mengekspor akun atau materi privat.';

    private string $baseUrl;
    private string $basePath;
    private array $routes = [];
    private array $media = [];
    private array $assetFiles = [];

    public function handle(Kernel $kernel): int
    {
        $originalRequest = app('request');
        $originalUrl = config('app.url');
        $originalDebug = config('app.debug');
        $originalTrustedHosts = Request::getTrustedHosts();

        try {
            $this->baseUrl = $this->httpsUrl((string) $this->option('url'));
            $this->basePath = rtrim(parse_url($this->baseUrl, PHP_URL_PATH) ?: '', '/');
            $portalUrl = $this->option('portal-url') ? $this->httpsUrl((string) $this->option('portal-url')) : '';
            $output = $this->outputDirectory((string) $this->option('output'));
            $this->routes = [];
            $this->media = [];
            $this->assetFiles = [];
            $this->collectRoutes();
            $this->collectMedia();

            config(['app.url' => $this->baseUrl, 'app.debug' => false]);
            URL::forceRootUrl($this->baseUrl);
            URL::forceScheme('https');

            $documents = [];
            foreach ($this->routes as $target => $source) {
                // Resolve routes locally. The export never requests an external server.
                $origin = 'https://'.parse_url($this->baseUrl, PHP_URL_HOST).(parse_url($this->baseUrl, PHP_URL_PORT) ? ':'.parse_url($this->baseUrl, PHP_URL_PORT) : '');
                $request = Request::create($origin.$source, 'GET');
                $response = $kernel->handle($request);
                if ($response->getStatusCode() !== 200) {
                    throw new RuntimeException('Halaman publik gagal dirender: '.$source.' (HTTP '.$response->getStatusCode().').');
                }
                $documents[$this->htmlPath($target)] = $this->rewrite((string) $response->getContent(), $target);
                $kernel->terminate($request, $response);
            }
            $documents['portal/index.html'] = $this->portalPage($portalUrl);
            $documents['404.html'] = $this->statusPage('Halaman tidak ditemukan', 'Halaman yang Anda tuju tidak tersedia. Silakan kembali ke beranda untuk menjelajahi informasi Ibnu Aqil.');
            $documents['sitemap.xml'] = $this->sitemap();
            $documents['robots.txt'] = "User-agent: *\nAllow: /\nDisallow: ".$this->basePath."/portal/\nSitemap: ".$this->baseUrl."/sitemap.xml\n";
            $documents['.nojekyll'] = '';

            // Add assets only after every page rendered successfully. No recursive delete.
            if (!is_dir($output) && !mkdir($output, 0755, true) && !is_dir($output)) {
                throw new RuntimeException('Direktori ekspor tidak dapat dibuat.');
            }
            foreach ($documents as $relative => $contents) {
                $this->write($output, $relative, $contents);
            }
            $this->collectStylesheetDependencies();
            foreach ($this->assetFiles as $relative => $source) {
                $this->copy($output, $relative, $source);
            }
            foreach ($this->media as $item) {
                $this->copy($output, $item['target'], $item['source']);
            }
            $this->info(count($this->routes).' halaman publik dan halaman status berhasil diekspor ke '.$output);
            $this->line('Akun, admin, database, materi pengimbasan, dan file privat tidak disertakan.');

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        } finally {
            config(['app.url' => $originalUrl, 'app.debug' => $originalDebug]);
            Request::setTrustedHosts($originalTrustedHosts);
            app()->instance('request', $originalRequest);
            app('url')->setRequest($originalRequest);
            URL::forceRootUrl($originalUrl);
            URL::forceScheme(parse_url((string) $originalUrl, PHP_URL_SCHEME) ?: null);
        }
    }

    // Stylesheets may reference fonts and decorative images with relative url(); copy those files too.
    private function collectStylesheetDependencies(): void
    {
        foreach (array_keys($this->assetFiles) as $relative) {
            if (!str_ends_with(strtolower($relative), '.css')) {
                continue;
            }
            preg_match_all('~url\(\s*([\'"]?)([^\'")]+)\1\s*\)~i', (string) file_get_contents($this->assetFiles[$relative]), $matches);
            foreach ($matches[2] as $reference) {
                if (preg_match('~^(?:data:|https?:|/|#)~i', $reference)) {
                    continue;
                }
                $path = strtok($reference, '?#');
                $segments = [];
                foreach (explode('/', dirname($relative).'/'.$path) as $segment) {
                    if ($segment === '' || $segment === '.') {
                        continue;
                    }
                    if ($segment === '..') {
                        array_pop($segments);
                        continue;
                    }
                    $segments[] = $segment;
                }
                $target = implode('/', $segments);
                if (!preg_match('~^assets/(?:[a-zA-Z0-9_-]+/)*[a-zA-Z0-9_.-]+\.(?:svg|png|jpg|jpeg|webp|gif|woff2?)$~i', $target) || !is_file(public_path($target)) || is_link(public_path($target))) {
                    throw new RuntimeException('Referensi aset stylesheet tidak dapat diekspor: '.$reference);
                }
                $this->assetFiles[$target] = public_path($target);
            }
        }
    }

    private function httpsUrl(string $url): string
    {
        $parts = parse_url($url);
        if (!filter_var($url, FILTER_VALIDATE_URL) || !$parts || ($parts['scheme'] ?? '') !== 'https' || empty($parts['host']) || isset($parts['user']) || isset($parts['pass']) || isset($parts['query']) || isset($parts['fragment']) || preg_match('~[\\\\\x00-\x20]|(?:^|/)\.\.(?:/|$)|%2e|%2f|%5c~i', $parts['path'] ?? '')) {
            throw new RuntimeException('Gunakan URL HTTPS lengkap tanpa kredensial, kueri, fragmen, atau traversal path.');
        }

        return rtrim($url, '/');
    }

    private function outputDirectory(string $name): string
    {
        $reserved = ['app', 'bootstrap', 'config', 'database', 'docs', 'public', 'resources', 'routes', 'storage', 'tests', 'vendor', 'node_modules'];
        if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9_-]{0,63}$/', $name) || in_array(strtolower($name), $reserved, true)) {
            throw new RuntimeException('--output harus berupa satu nama direktori baru, misalnya pages; direktori sumber aplikasi dan traversal ditolak.');
        }
        $output = base_path($name);
        if (is_link($output) || (file_exists($output) && !is_dir($output))) {
            throw new RuntimeException('Lokasi ekspor harus direktori biasa.');
        }
        if (is_dir($output)) {
            $expected = strtolower(str_replace('\\', '/', $output));
            $actual = strtolower(str_replace('\\', '/', (string) realpath($output)));
            if ($expected !== $actual || count(array_diff(scandir($output), ['.', '..'])) > 0) {
                throw new RuntimeException('Direktori ekspor harus baru atau kosong. Pilih --output lain; file yang sudah ada tidak dihapus.');
            }
        }

        return $output;
    }

    private function collectRoutes(): void
    {
        $this->routes['/'] = '/';
        foreach (['prestasi' => 'achievement'] as $path => $kind) {
            $this->routes['/'.$path] = '/'.$path;
            $lastPage = max(1, (int) ceil(Article::published()->where('kind', $kind)->count() / 12));
            for ($page = 2; $page <= $lastPage; $page++) {
                $this->routes['/'.$path.'/page/'.$page] = '/'.$path.'?page='.$page;
            }
        }
        foreach (ContentPage::published()->orderBy('id')->get() as $page) {
            $this->assertSlug($page->slug);
            $path = $page->type === 'unit' ? '/unit/'.$page->slug : (in_array($page->slug, ['tentang', 'fasilitas', 'pendaftaran', 'kontak'], true) ? '/'.$page->slug : '/halaman/'.$page->slug);
            $this->routes[$path] = $path;
        }
    }

    private function collectMedia(): void
    {
        $ids = Article::published()->whereNotNull('cover_asset_id')->pluck('cover_asset_id');
        foreach (Asset::whereIn('id', $ids)->where('visibility', 'public')->get() as $asset) {
            $extension = match ($asset->mime_type) {'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', default => null};
            if (!$extension || !preg_match('~^media/[a-zA-Z0-9/_-]+\.(?:jpg|jpeg|png|webp)$~', $asset->path) || !Storage::disk('private')->exists($asset->path)) {
                throw new RuntimeException('Sampul publik tidak valid atau berkas tidak tersedia: aset #'.$asset->id);
            }
            $this->media[(string) $asset->id] = ['target' => 'media/'.$asset->id.'.'.$extension, 'source' => Storage::disk('private')->path($asset->path)];
        }
    }

    private function assertSlug(string $slug): void
    {
        if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
            throw new RuntimeException('Slug konten publik tidak aman untuk ekspor.');
        }
    }

    private function rewrite(string $html, string $target): string
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="UTF-8">'.$html, LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        $xpath = new DOMXPath($document);

        foreach (iterator_to_array($xpath->query('//form | //meta[@name="csrf-token"] | //input[@name="_token"]')) as $node) {
            $node->parentNode?->removeChild($node);
        }
        foreach (iterator_to_array($xpath->query('//*[@href or @src]')) as $node) {
            foreach (['href', 'src'] as $attribute) {
                if (!$node instanceof DOMElement || !$node->hasAttribute($attribute)) {
                    continue;
                }
                $value = $node->getAttribute($attribute);
                $rewritten = $this->link($value);
                if ($rewritten === null) {
                    if ($attribute === 'src') {
                        throw new RuntimeException('Referensi aset publik tidak dapat diekspor.');
                    }
                    $node->parentNode?->removeChild($node);
                    break;
                }
                $node->setAttribute($attribute, $rewritten);
            }
        }
        foreach (iterator_to_array($xpath->query('//details[not(.//a)]')) as $node) {
            $node->parentNode?->removeChild($node);
        }
        foreach ($xpath->query('//link[@rel="canonical"]') as $node) {
            $node->setAttribute('href', $this->pageUrl($target));
        }
        foreach ($xpath->query('//meta[@property="og:url"]') as $node) {
            $node->setAttribute('content', $this->pageUrl($target));
        }
        $output = $document->saveHTML();

        return preg_replace('/<\?xml encoding="UTF-8">\s*/', '', $output);
    }

    private function link(string $value): ?string
    {
        if ($value === '' || str_starts_with($value, '#') || preg_match('~^(mailto:|tel:|data:)~i', $value)) {
            return $value;
        }
        $parts = parse_url($value);
        if (!$parts) {
            return null;
        }
        $host = $parts['host'] ?? null;
        if ($host && !in_array(strtolower($host), [strtolower((string) parse_url($this->baseUrl, PHP_URL_HOST)), 'export.invalid'], true)) {
            return $value;
        }
        $path = $parts['path'] ?? '/';
        if ($this->basePath !== '' && ($path === $this->basePath || str_starts_with($path, $this->basePath.'/'))) {
            $path = substr($path, strlen($this->basePath)) ?: '/';
        }
        $path = '/'.trim($path, '/');
        $fragment = isset($parts['fragment']) ? '#'.$parts['fragment'] : '';
        if (preg_match('~^/(portal|login|admin|password|akun)(?:/|$)~', $path)) {
            return $this->pageUrl('/portal').$fragment;
        }
        if (str_starts_with($path, '/assets/')) {
            $relative = ltrim($path, '/');
            if (!preg_match('~^assets/(?:[a-zA-Z0-9_-]+/)*[a-zA-Z0-9_.-]+\.(?:css|js|svg|png|jpg|jpeg|webp|gif|ico|webmanifest|pdf)$~i', $relative) || str_contains($relative, '..') || !is_file(public_path($relative)) || is_link(public_path($relative))) {
                return null;
            }
            $this->assetFiles[$relative] = public_path($relative);

            return $this->baseUrl.'/'.$relative;
        }
        if (preg_match('~^/media/(\d+)$~', $path, $match)) {
            return isset($this->media[$match[1]]) ? $this->baseUrl.'/'.$this->media[$match[1]]['target'] : null;
        }
        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
            if (isset($query['page']) && ctype_digit((string) $query['page']) && (int) $query['page'] > 1) {
                $path .= '/page/'.(int) $query['page'];
            }
        }

        return isset($this->routes[$path]) ? $this->pageUrl($path).$fragment : null;
    }

    private function htmlPath(string $path): string
    {
        return trim($path, '/').($path === '/' ? '' : '/').'index.html';
    }

    private function pageUrl(string $path): string
    {
        return $this->baseUrl.($path === '/' ? '/' : '/'.trim($path, '/').'/');
    }

    private function portalPage(string $portalUrl): string
    {
        $settings = Setting::pluck('value', 'key');
        $actions = $portalUrl ? '<a class="button" href="'.e($portalUrl).'" rel="noopener noreferrer">Buka halaman masuk pengelola <span aria-hidden="true">↗</span></a>' : '<p class="content-status">Halaman masuk pengelola tidak tersedia pada versi statis website ini.</p>';
        $whatsapp = (string) ($settings['whatsapp_url'] ?? '');
        if (preg_match('~^https://wa\.me/[0-9]{9,15}$~', $whatsapp)) {
            $actions .= '<p style="margin-top:24px"><a class="text-link" href="'.e($whatsapp).'" target="_blank" rel="noopener noreferrer">Tanya admin PPDB melalui WhatsApp <span aria-hidden="true">↗</span></a></p>';
        }

        return $this->statusPage('Akses pengelola', 'Halaman ini khusus untuk pengelola website Ibnu Aqil. Orang tua dan calon santri dapat langsung bertanya kepada admin PPDB.', $actions);
    }

    private function statusPage(string $title, string $description, string $actions = ''): string
    {
        $css = public_path('assets/site.css');
        if (is_file($css)) {
            $this->assetFiles['assets/site.css'] = $css;
        }
        $home = e($this->pageUrl('/'));

        return '<!doctype html><html lang="id"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex,follow"><title>'.e($title).' — Ibnu Aqil Education Center</title><meta name="description" content="'.e($description).'"><link rel="stylesheet" href="'.e($this->baseUrl).'/assets/site.css"></head><body><a class="skip-link" href="#main-content">Lewati ke konten utama</a><header class="site-header"><div class="container header-inner"><a class="brand" href="'.$home.'"><span class="brand-name">Ibnu Aqil<small>EDUCATION CENTER</small></span></a><a class="text-link" href="'.$home.'">Kembali ke beranda <span aria-hidden="true">↗</span></a></div></header><main id="main-content"><section class="page-hero"><div class="container page-hero-grid"><div><p class="eyebrow">Ibnu Aqil Education Center</p><h1>'.e($title).'</h1><p class="page-lead">'.e($description).'</p></div><span class="page-ornament" aria-hidden="true">✳</span></div></section><section class="section"><div class="container prose">'.$actions.'<p style="margin-top:32px"><a class="text-link" href="'.$home.'">← Jelajahi website Ibnu Aqil</a></p></div></section></main></body></html>';
    }

    private function sitemap(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach (array_keys($this->routes) as $path) {
            $xml .= '<url><loc>'.htmlspecialchars($this->pageUrl($path), ENT_XML1).'</loc></url>';
        }

        return $xml.'</urlset>';
    }

    private function write(string $output, string $relative, string $contents): void
    {
        $path = $output.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
        if (!is_dir(dirname($path)) && !mkdir(dirname($path), 0755, true) && !is_dir(dirname($path))) {
            throw new RuntimeException('Subdirektori ekspor tidak dapat dibuat.');
        }
        if (file_put_contents($path, $contents) === false) {
            throw new RuntimeException('Berkas ekspor tidak dapat ditulis.');
        }
    }

    private function copy(string $output, string $relative, string $source): void
    {
        $path = $output.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
        if (!is_dir(dirname($path)) && !mkdir(dirname($path), 0755, true) && !is_dir(dirname($path))) {
            throw new RuntimeException('Subdirektori aset tidak dapat dibuat.');
        }
        if (!copy($source, $path)) {
            throw new RuntimeException('Aset publik tidak dapat disalin.');
        }
    }
}
