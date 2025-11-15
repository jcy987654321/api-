<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Config\Database;

class ApiController extends BaseController
{
    public function index(): void
    {
        $category = $_GET['category'] ?? '';
        
        // Fetch APIs from database
        $apis = $this->getApis($category);
        $categories = $this->getCategories();
        
        $this->renderView('api_list', [
            'page_type' => 'api_list',
            'apis' => $apis,
            'categories' => $categories,
            'current_category' => $category,
        ]);
    }

    public function detail(string $slug): void
    {
        $api = $this->getApiBySlug($slug);
        
        if (!$api) {
            http_response_code(404);
            $this->renderView('404', ['page_type' => 'error']);
            return;
        }
        
        // Get related APIs
        $relatedApis = $this->getRelatedApis($api['category'], $api['id']);
        
        $this->renderView('api_detail', [
            'page_type' => 'api_detail',
            'api' => $api,
            'related_apis' => $relatedApis,
        ]);
    }

    private function getApis(string $category = ''): array
    {
        $db = Database::getConnection();
        
        if ($category) {
            $stmt = $db->prepare("
                SELECT id, name, slug, description, category, status, created_at, updated_at
                FROM apis 
                WHERE LOWER(category) = LOWER(?) AND status = 'active'
                ORDER BY name ASC
            ");
            $stmt->execute([$category]);
        } else {
            $stmt = $db->query("
                SELECT id, name, slug, description, category, status, created_at, updated_at
                FROM apis 
                WHERE status = 'active'
                ORDER BY name ASC
            ");
        }
        
        return $stmt->fetchAll() ?: [];
    }

    private function getCategories(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT DISTINCT category, COUNT(*) as count
            FROM apis 
            WHERE status = 'active'
            GROUP BY category
            ORDER BY count DESC, category ASC
        ");
        
        return $stmt->fetchAll() ?: [];
    }

    private function getApiBySlug(string $slug): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT id, name, slug, description, category, status, documentation_url, 
                   created_at, updated_at
            FROM apis 
            WHERE slug = ?
        ");
        $stmt->execute([$slug]);
        
        return $stmt->fetch() ?: null;
    }

    private function getRelatedApis(string $category, int $excludeId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT id, name, slug, description, category
            FROM apis 
            WHERE category = ? AND id != ? AND status = 'active'
            ORDER BY name ASC
            LIMIT 5
        ");
        $stmt->execute([$category, $excludeId]);
        
        return $stmt->fetchAll() ?: [];
    }
}