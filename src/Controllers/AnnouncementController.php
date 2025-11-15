<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Config\Database;

class AnnouncementController extends BaseController
{
    public function index(): void
    {
        $page = (int) ($_GET['page'] ?? 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        // Fetch announcements from database
        $announcements = $this->getAnnouncements($limit, $offset);
        $total = $this->getAnnouncementsCount();
        $totalPages = (int) ceil($total / $limit);
        
        $this->renderView('announcements', [
            'page_type' => 'announcements',
            'announcements' => $announcements,
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total' => $total,
        ]);
    }

    public function detail(string $slug): void
    {
        $announcement = $this->getAnnouncementBySlug($slug);
        
        if (!$announcement) {
            http_response_code(404);
            $this->renderView('404', ['page_type' => 'error']);
            return;
        }
        
        // Get recent announcements
        $recentAnnouncements = $this->getRecentAnnouncements($announcement['id']);
        
        $this->renderView('announcement_detail', [
            'page_type' => 'announcement_detail',
            'announcement' => $announcement,
            'recent_announcements' => $recentAnnouncements,
        ]);
    }

    private function getAnnouncements(int $limit, int $offset): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT id, title, slug, summary, author, created_at, updated_at
            FROM announcements 
            WHERE status = 'published'
            ORDER BY created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$limit, $offset]);
        
        return $stmt->fetchAll() ?: [];
    }

    private function getAnnouncementsCount(): int
    {
        $db = Database::getConnection();
        $stmt = $db->query("
            SELECT COUNT(*) as count
            FROM announcements 
            WHERE status = 'published'
        ");
        
        $result = $stmt->fetch();
        return (int) ($result['count'] ?? 0);
    }

    private function getAnnouncementBySlug(string $slug): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT id, title, slug, summary, content, author, created_at, updated_at
            FROM announcements 
            WHERE slug = ? AND status = 'published'
        ");
        $stmt->execute([$slug]);
        
        return $stmt->fetch() ?: null;
    }

    private function getRecentAnnouncements(int $excludeId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("
            SELECT id, title, slug, created_at
            FROM announcements 
            WHERE id != ? AND status = 'published'
            ORDER BY created_at DESC
            LIMIT 5
        ");
        $stmt->execute([$excludeId]);
        
        return $stmt->fetchAll() ?: [];
    }
}