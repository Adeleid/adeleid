<?php

declare(strict_types=1);

require __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('admin.php');
}

$action = $_POST['action'] ?? '';

try {
    $pdo = db();
    if ($action === 'create') {
        $name = trim((string)($_POST['name'] ?? ''));
        $url = trim((string)($_POST['url'] ?? ''));

        if ($name === '' || $url === '') {
            throw new RuntimeException('الاسم والرابط مطلوبان.');
        }

        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new RuntimeException('رابط غير صالح.');
        }

        $stmt = $pdo->prepare('INSERT INTO sources (name, url, active) VALUES (:name, :url, 1)');
        $stmt->execute([':name' => $name, ':url' => $url]);
        redirect('admin.php?message=' . rawurlencode('تمت إضافة المصدر بنجاح'));
    }

    if ($action === 'toggle') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            throw new RuntimeException('معرّف مصدر غير صالح.');
        }

        $stmt = $pdo->prepare('UPDATE sources SET active = CASE active WHEN 1 THEN 0 ELSE 1 END, updated_at = CURRENT_TIMESTAMP WHERE id = :id');
        $stmt->execute([':id' => $id]);
        redirect('admin.php?message=' . rawurlencode('تم تحديث حالة المصدر'));
    }

    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            throw new RuntimeException('معرّف مصدر غير صالح.');
        }

        $stmt = $pdo->prepare('DELETE FROM sources WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $pdo->prepare('DELETE FROM items WHERE source_id = :id')->execute([':id' => $id]);
        redirect('admin.php?message=' . rawurlencode('تم حذف المصدر'));
    }

    throw new RuntimeException('إجراء غير مدعوم.');
} catch (Throwable $e) {
    redirect('admin.php?message=' . rawurlencode('خطأ: ' . $e->getMessage()));
}
