<section class="page-header">
    <h1>API Directory</h1>
    <p>Discover and explore APIs across various categories</p>
</section>

<section class="api-filters">
    <div class="filter-controls">
        <form method="GET" action="/apis">
            <label for="category">Filter by Category:</label>
            <select name="category" id="category" onchange="this.form.submit()">
                <option value="">All Categories</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= htmlspecialchars($category['category']) ?>" 
                            <?= $current_category === $category['category'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($category['category']) ?> (<?= $category['count'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
</section>

<section class="api-list">
    <?php if (empty($apis)): ?>
        <div class="no-results">
            <h3>No APIs Found</h3>
            <p>
                <?php if ($current_category): ?>
                    No APIs found in the "<?= htmlspecialchars($current_category) ?>" category.
                    <a href="/apis">Browse all categories</a>
                <?php else: ?>
                    No APIs are currently available. Check back soon!
                <?php endif; ?>
            </p>
        </div>
    <?php else: ?>
        <div class="api-grid">
            <?php foreach ($apis as $api): ?>
                <article class="api-card">
                    <div class="api-header">
                        <h3><a href="/api/<?= htmlspecialchars($api['slug']) ?>"><?= htmlspecialchars($api['name']) ?></a></h3>
                        <span class="api-category"><?= htmlspecialchars($api['category']) ?></span>
                        <span class="api-status status-<?= htmlspecialchars($api['status']) ?>">
                            <?= htmlspecialchars(ucfirst($api['status'])) ?>
                        </span>
                    </div>
                    <div class="api-description">
                        <p><?= htmlspecialchars(substr($api['description'], 0, 200)) ?>...</p>
                    </div>
                    <div class="api-meta">
                        <small>Updated <?= date('M j, Y', strtotime($api['updated_at'])) ?></small>
                    </div>
                    <div class="api-actions">
                        <a href="/api/<?= htmlspecialchars($api['slug']) ?>" class="btn btn-primary">View Details</a>
                        <?php if (!empty($api['documentation_url'])): ?>
                            <a href="<?= htmlspecialchars($api['documentation_url']) ?>" class="btn btn-secondary" target="_blank" rel="noopener">Documentation</a>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>