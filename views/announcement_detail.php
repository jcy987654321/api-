<section class="page-header">
    <h1><?= htmlspecialchars($announcement['title']) ?></h1>
    <div class="announcement-meta">
        <span class="announcement-date">
            <?= date('F j, Y', strtotime($announcement['created_at'])) ?>
        </span>
        <?php if (!empty($announcement['author'])): ?>
            <span class="announcement-author">
                by <?= htmlspecialchars($announcement['author']) ?>
            </span>
        <?php endif; ?>
        <?php 
        $updated = strtotime($announcement['updated_at']);
        $created = strtotime($announcement['created_at']);
        if ($updated > $created): ?>
            <span class="announcement-updated">
                Updated <?= date('F j, Y', $updated) ?>
            </span>
        <?php endif; ?>
    </div>
</section>

<section class="announcement-content">
    <div class="announcement-body">
        <div class="announcement-text">
            <?= $announcement['content'] // Assuming this contains HTML content ?>
        </div>
    </div>
</section>

<section class="announcement-footer">
    <div class="announcement-actions">
        <button class="btn btn-secondary" onclick="window.print()">
            Print Announcement
        </button>
        <button class="btn btn-secondary" onclick="navigator.share ? navigator.share({title: '<?= htmlspecialchars($announcement['title']) ?>', url: window.location.href}) : navigator.clipboard.writeText(window.location.href)">
            Share
        </button>
    </div>

    <div class="breadcrumbs">
        <a href="/">Home</a> &raquo; 
        <a href="/announcements">Announcements</a> &raquo; 
        <span><?= htmlspecialchars($announcement['title']) ?></span>
    </div>
</section>

<?php if (!empty($recent_announcements)): ?>
<section class="recent-announcements">
    <h2>Recent Announcements</h2>
    <div class="recent-list">
        <?php foreach ($recent_announcements as $recent): ?>
            <article class="recent-item">
                <h3><a href="/announcement/<?= htmlspecialchars($recent['slug']) ?>"><?= htmlspecialchars($recent['title']) ?></a></h3>
                <p class="recent-date"><?= date('F j, Y', strtotime($recent['created_at'])) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>