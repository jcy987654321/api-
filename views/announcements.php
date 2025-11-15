<section class="page-header">
    <h1>Announcements</h1>
    <p>Stay updated with the latest news and updates</p>
</section>

<section class="announcements-list">
    <?php if (empty($announcements)): ?>
        <div class="no-results">
            <h3>No Announcements</h3>
            <p>No announcements are available at this time. Check back soon for updates!</p>
        </div>
    <?php else: ?>
        <div class="announcement-items">
            <?php foreach ($announcements as $announcement): ?>
                <article class="announcement-card">
                    <div class="announcement-header">
                        <h2><a href="/announcement/<?= htmlspecialchars($announcement['slug']) ?>"><?= htmlspecialchars($announcement['title']) ?></a></h2>
                        <div class="announcement-meta">
                            <span class="announcement-date">
                                <?= date('F j, Y', strtotime($announcement['created_at'])) ?>
                            </span>
                            <?php if (!empty($announcement['author'])): ?>
                                <span class="announcement-author">
                                    by <?= htmlspecialchars($announcement['author']) ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="announcement-summary">
                        <p><?= htmlspecialchars($announcement['summary']) ?></p>
                    </div>
                    <div class="announcement-actions">
                        <a href="/announcement/<?= htmlspecialchars($announcement['slug']) ?>" class="btn btn-primary">
                            Read More
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php if ($current_page > 1): ?>
                    <a href="?page=<?= $current_page - 1 ?>" class="pagination-link">&laquo; Previous</a>
                <?php endif; ?>

                <?php
                $start = max(1, $current_page - 2);
                $end = min($total_pages, $current_page + 2);
                
                if ($start > 1) {
                    echo '<a href="?page=1" class="pagination-link">1</a>';
                    if ($start > 2) echo '<span class="pagination-ellipsis">...</span>';
                }
                
                for ($i = $start; $i <= $end; $i++) {
                    $class = $i === $current_page ? 'pagination-link active' : 'pagination-link';
                    echo "<a href=\"?page={$i}\" class=\"{$class}\">{$i}</a>";
                }
                
                if ($end < $total_pages) {
                    if ($end < $total_pages - 1) echo '<span class="pagination-ellipsis">...</span>';
                    echo "<a href=\"?page={$total_pages}\" class=\"pagination-link\">{$total_pages}</a>";
                }
                ?>

                <?php if ($current_page < $total_pages): ?>
                    <a href="?page=<?= $current_page + 1 ?>" class="pagination-link">Next &raquo;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</section>

<section class="rss-feed-info">
    <div class="rss-cta">
        <h3>Subscribe to Updates</h3>
        <p>Never miss an announcement! Subscribe to our RSS feed to get the latest news delivered directly to your feed reader.</p>
        <a href="/rss.xml" class="btn btn-secondary">
            <img src="/assets/images/rss-icon.png" alt="RSS" width="16" height="16">
            Subscribe to RSS Feed
        </a>
    </div>
</section>