<section class="page-header">
    <h1><?= htmlspecialchars($api['name']) ?></h1>
    <div class="api-meta-info">
        <span class="api-category"><?= htmlspecialchars($api['category']) ?></span>
        <span class="api-status status-<?= htmlspecialchars($api['status']) ?>">
            <?= htmlspecialchars(ucfirst($api['status'])) ?>
        </span>
    </div>
</section>

<section class="api-details">
    <div class="api-main">
        <div class="api-description">
            <h2>Description</h2>
            <p><?= nl2br(htmlspecialchars($api['description'])) ?></p>
        </div>

        <div class="api-info">
            <h2>API Information</h2>
            <dl class="api-details-list">
                <dt>Status</dt>
                <dd><?= htmlspecialchars(ucfirst($api['status'])) ?></dd>
                
                <dt>Category</dt>
                <dd><?= htmlspecialchars($api['category']) ?></dd>
                
                <dt>Added</dt>
                <dd><?= date('F j, Y', strtotime($api['created_at'])) ?></dd>
                
                <dt>Last Updated</dt>
                <dd><?= date('F j, Y', strtotime($api['updated_at'])) ?></dd>
                
                <?php if (!empty($api['documentation_url'])): ?>
                    <dt>Documentation</dt>
                    <dd><a href="<?= htmlspecialchars($api['documentation_url']) ?>" target="_blank" rel="noopener">View Documentation</a></dd>
                <?php endif; ?>
            </dl>
        </div>

        <div class="api-endpoints">
            <h2>Available Endpoints</h2>
            <div class="endpoint-list">
                <!-- This would be populated from actual API data -->
                <div class="endpoint">
                    <code class="method GET">GET</code>
                    <code class="path">/api/v1/resource</code>
                    <p class="endpoint-description">Retrieve a list of resources</p>
                </div>
                <div class="endpoint">
                    <code class="method POST">POST</code>
                    <code class="path">/api/v1/resource</code>
                    <p class="endpoint-description">Create a new resource</p>
                </div>
            </div>
        </div>
    </div>

    <aside class="api-sidebar">
        <div class="api-actions">
            <h3>Actions</h3>
            <?php if (!empty($api['documentation_url'])): ?>
                <a href="<?= htmlspecialchars($api['documentation_url']) ?>" class="btn btn-primary btn-block" target="_blank" rel="noopener">
                    View Documentation
                </a>
            <?php endif; ?>
            <button class="btn btn-secondary btn-block" onclick="window.print()">
                Print Details
            </button>
        </div>

        <?php if (!empty($related_apis)): ?>
            <div class="related-apis">
                <h3>Related APIs</h3>
                <ul class="related-list">
                    <?php foreach ($related_apis as $relatedApi): ?>
                        <li>
                            <a href="/api/<?= htmlspecialchars($relatedApi['slug']) ?>">
                                <?= htmlspecialchars($relatedApi['name']) ?>
                            </a>
                            <small class="related-category"><?= htmlspecialchars($relatedApi['category']) ?></small>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
    </aside>
</section>

<section class="api-footer">
    <div class="breadcrumbs">
        <a href="/">Home</a> &raquo; 
        <a href="/apis">APIs</a> &raquo; 
        <span><?= htmlspecialchars($api['name']) ?></span>
    </div>
</section>