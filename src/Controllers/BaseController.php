<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\MetaService;

abstract class BaseController
{
    protected MetaService $metaService;

    public function __construct()
    {
        $this->metaService = new MetaService();
    }

    protected function renderView(string $template, array $data = []): void
    {
        $meta = $this->metaService->generatePageMeta($data['page_type'] ?? 'default', $data);
        $jsonLd = $this->metaService->generateJsonLd($meta, $data);
        
        $viewData = array_merge($data, [
            'meta' => $meta,
            'jsonLd' => $jsonLd,
        ]);
        
        // Include the layout template
        include ROOT_PATH . '/views/layout.php';
    }

    protected function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }
}