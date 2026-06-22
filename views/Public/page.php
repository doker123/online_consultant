<?php /** @var $page Page */ ?>
<div class="page-container">
    <?php if ($page->image): ?>
        <div class="page-image">
            <img src="<?= app()->route->getUrl('/public/uploads/pages/' . $page->image) ?>" alt="<?= htmlspecialchars($page->title) ?>">
        </div>
    <?php endif; ?>
    <h1><?= htmlspecialchars($page->title) ?></h1>
    <div class="page-content">
        <?= nl2br(htmlspecialchars($page->content)) ?>
    </div>
    <div class="page-meta">
        <span>Опубликовано: <?= date('d.m.Y H:i', strtotime($page->created_at)) ?></span>
    </div>
</div>
