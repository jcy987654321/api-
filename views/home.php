<section class="hero">
    <div class="hero-content">
        <h1><?= htmlspecialchars(\App\Config\Settings::get('site.name')) ?></h1>
        <p><?= htmlspecialchars(\App\Config\Settings::get('site.description')) ?></p>
        <div class="hero-actions">
            <a href="/apis" class="btn btn-primary">Browse APIs</a>
            <a href="/announcements" class="btn btn-secondary">Latest Updates</a>
        </div>
    </div>
</section>

<section class="stats">
    <div class="stats-grid">
        <div class="stat-item">
            <h3><?= number_format($stats['total_apis']) ?></h3>
            <p>Total APIs</p>
        </div>
        <div class="stat-item">
            <h3><?= number_format($stats['active_apis']) ?></h3>
            <p>Active APIs</p>
        </div>
        <div class="stat-item">
            <h3><?= number_format($stats['total_announcements']) ?></h3>
            <p>Announcements</p>
        </div>
    </div>
</section>

<section class="featured-apis">
    <h2>Featured APIs</h2>
    <?php if (empty($featured_apis)): ?>
        <p>No featured APIs available yet. Check back soon!</p>
    <?php else: ?>
        <div class="api-grid">
            <?php foreach ($featured_apis as $api): ?>
                <div class="api-card">
                    <h3><?= htmlspecialchars($api['name']) ?></h3>
                    <p><?= htmlspecialchars(substr($api['description'], 0, 150)) ?>...</p>
                    <a href="/api/<?= htmlspecialchars($api['slug']) ?>" class="btn btn-sm">View Details</a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<section class="latest-announcements">
    <h2>Latest Announcements</h2>
    <?php if (empty($latest_announcements)): ?>
        <p>No announcements available yet. Check back soon!</p>
    <?php else: ?>
        <div class="announcement-list">
            <?php foreach ($latest_announcements as $announcement): ?>
                <article class="announcement-item">
                    <h3><a href="/announcement/<?= htmlspecialchars($announcement['slug']) ?>"><?= htmlspecialchars($announcement['title']) ?></a></h3>
                    <p class="announcement-meta">Posted on <?= date('F j, Y', strtotime($announcement['created_at'])) ?></p>
                    <p><?= htmlspecialchars(substr(strip_tags($announcement['summary'] ?? $announcement['content']), 0, 200)) ?>...</p>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>