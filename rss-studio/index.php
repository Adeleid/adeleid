<?php

declare(strict_types=1);

require __DIR__ . '/config.php';

try {
    $pdo = db();
    $sources = $pdo->query('SELECT id, name, url, active, updated_at FROM sources ORDER BY id DESC')->fetchAll();

    $itemsStmt = $pdo->prepare(
    'SELECT items.title, items.link, items.description, items.pub_date, sources.name AS source_name
     FROM items
     JOIN sources ON sources.id = items.source_id
     ORDER BY COALESCE(items.pub_date, items.created_at) DESC
     LIMIT :limit'
);
$itemsStmt->bindValue(':limit', FEED_LIMIT, PDO::PARAM_INT);
$itemsStmt->execute();
$items = $itemsStmt->fetchAll();
} catch (Throwable $e) {
    render_fatal($e);
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?= h(APP_NAME) ?></title>
  <style>
    body { font-family: system-ui, sans-serif; margin: 2rem; max-width: 900px; }
    .top { display:flex; gap:1rem; flex-wrap: wrap; margin-bottom: 1.5rem; }
    .box { border:1px solid #ddd; border-radius:8px; padding:1rem; margin-bottom:1rem; }
    .muted { color:#666; font-size:.9rem; }
    a { color:#0b57d0; text-decoration:none; }
    a:hover { text-decoration:underline; }
    ul { padding-right:1rem; }
  </style>
</head>
<body>
  <h1><?= h(APP_NAME) ?></h1>
  <div class="top">
    <a href="admin.php">لوحة الإدارة</a>
    <a href="feed.php" target="_blank">رابط الـ RSS المجمّع</a>
    <a href="cron.php" target="_blank">تشغيل تحديث الآن</a>
  </div>

  <div class="box">
    <h2>المصادر (<?= count($sources) ?>)</h2>
    <?php if (!$sources): ?>
      <p class="muted">لا توجد مصادر بعد.</p>
    <?php else: ?>
      <ul>
        <?php foreach ($sources as $source): ?>
          <li>
            <strong><?= h($source['name']) ?></strong>
            — <a href="<?= h($source['url']) ?>" target="_blank" rel="noopener"><?= h($source['url']) ?></a>
            — الحالة: <?= (int)$source['active'] === 1 ? 'مفعّل' : 'موقوف' ?>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif; ?>
  </div>

  <div class="box">
    <h2>آخر الأخبار</h2>
    <?php if (!$items): ?>
      <p class="muted">لا توجد عناصر. أضف مصادر ثم شغّل التحديث.</p>
    <?php else: ?>
      <?php foreach ($items as $item): ?>
        <article style="margin-bottom: 1rem;">
          <h3 style="margin:.2rem 0;"><a href="<?= h($item['link']) ?>" target="_blank" rel="noopener"><?= h($item['title']) ?></a></h3>
          <div class="muted"><?= h($item['source_name']) ?><?= $item['pub_date'] ? ' — ' . h($item['pub_date']) : '' ?></div>
          <?php if (!empty($item['description'])): ?>
            <p><?= h(strip_tags((string)$item['description'])) ?></p>
          <?php endif; ?>
        </article>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</body>
</html>
