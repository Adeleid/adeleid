<?php

declare(strict_types=1);

require __DIR__ . '/config.php';

try {
    $pdo = db();
    $message = $_GET['message'] ?? '';
    $sources = $pdo->query('SELECT id, name, url, active, created_at, updated_at FROM sources ORDER BY id DESC')->fetchAll();
} catch (Throwable $e) {
    render_fatal($e);
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>إدارة المصادر - <?= h(APP_NAME) ?></title>
  <style>
    body { font-family: system-ui, sans-serif; margin: 2rem; max-width: 900px; }
    form { margin-bottom: 1.5rem; padding:1rem; border:1px solid #ddd; border-radius:8px; }
    input[type="text"], input[type="url"] { width: 100%; padding: .5rem; margin-bottom: .75rem; }
    table { width:100%; border-collapse: collapse; }
    th, td { border:1px solid #eee; padding:.5rem; text-align:right; vertical-align: top; }
    .ok { background:#e8f5e9; padding:.5rem; border-radius:6px; }
    button { padding: .4rem .8rem; }
    .row-actions form { display:inline; border:none; padding:0; margin:0 .25rem 0 0; }
  </style>
</head>
<body>
  <h1>لوحة الإدارة</h1>
  <p><a href="index.php">← الرجوع للرئيسية</a></p>

  <?php if ($message): ?>
    <p class="ok"><?= h($message) ?></p>
  <?php endif; ?>

  <form method="post" action="source.php">
    <h2>إضافة مصدر RSS</h2>
    <label>اسم المصدر</label>
    <input type="text" name="name" required>

    <label>رابط RSS</label>
    <input type="url" name="url" required placeholder="https://example.com/feed.xml">

    <button type="submit" name="action" value="create">إضافة</button>
  </form>

  <h2>المصادر الحالية</h2>
  <table>
    <thead>
      <tr>
        <th>#</th>
        <th>الاسم</th>
        <th>الرابط</th>
        <th>الحالة</th>
        <th>إجراءات</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$sources): ?>
        <tr><td colspan="5">لا توجد مصادر حتى الآن.</td></tr>
      <?php else: ?>
        <?php foreach ($sources as $source): ?>
          <tr>
            <td><?= (int)$source['id'] ?></td>
            <td><?= h($source['name']) ?></td>
            <td><a href="<?= h($source['url']) ?>" target="_blank" rel="noopener"><?= h($source['url']) ?></a></td>
            <td><?= (int)$source['active'] === 1 ? 'مفعّل' : 'موقوف' ?></td>
            <td class="row-actions">
              <form method="post" action="source.php">
                <input type="hidden" name="id" value="<?= (int)$source['id'] ?>">
                <button type="submit" name="action" value="toggle"><?= (int)$source['active'] === 1 ? 'إيقاف' : 'تفعيل' ?></button>
              </form>
              <form method="post" action="source.php" onsubmit="return confirm('متأكد من حذف المصدر؟');">
                <input type="hidden" name="id" value="<?= (int)$source['id'] ?>">
                <button type="submit" name="action" value="delete">حذف</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</body>
</html>
