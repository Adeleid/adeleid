<?php

declare(strict_types=1);

const APP_NAME = 'RSS Studio';
const APP_ROOT = __DIR__;
const DB_PATH = APP_ROOT . '/db.sqlite';
const FEED_LIMIT = 50;

function ensure_runtime_ready(): void
{
    if (!extension_loaded('pdo_sqlite')) {
        throw new RuntimeException('امتداد pdo_sqlite غير مفعّل على السيرفر.');
    }

    if (!extension_loaded('simplexml')) {
        throw new RuntimeException('امتداد simplexml غير مفعّل على السيرفر.');
    }

    if (!file_exists(DB_PATH) && !touch(DB_PATH)) {
        throw new RuntimeException('تعذر إنشاء قاعدة البيانات: ' . DB_PATH);
    }

    @chmod(DB_PATH, 0666);

    if (!is_writable(DB_PATH)) {
        throw new RuntimeException('ملف قاعدة البيانات غير قابل للكتابة: ' . DB_PATH);
    }
}

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    ensure_runtime_ready();

    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA foreign_keys = ON');

    init_db($pdo);

    return $pdo;
}

function init_db(PDO $pdo): void
{
    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS sources (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            url TEXT NOT NULL UNIQUE,
            active INTEGER NOT NULL DEFAULT 1,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )'
    );

    $pdo->exec(
        'CREATE TABLE IF NOT EXISTS items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            source_id INTEGER NOT NULL,
            guid TEXT NOT NULL,
            title TEXT NOT NULL,
            link TEXT NOT NULL,
            description TEXT,
            pub_date TEXT,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            UNIQUE(source_id, guid),
            FOREIGN KEY(source_id) REFERENCES sources(id) ON DELETE CASCADE
        )'
    );

    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_items_pub_date ON items(pub_date DESC)');
    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_sources_active ON sources(active)');
}

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirect(string $location): void
{
    header('Location: ' . $location);
    exit;
}

function render_fatal(Throwable $e): void
{
    http_response_code(500);
    header('Content-Type: text/html; charset=UTF-8');
    echo '<h1>حدث خطأ في إعداد التطبيق</h1>';
    echo '<p>' . h($e->getMessage()) . '</p>';
    exit;
}
