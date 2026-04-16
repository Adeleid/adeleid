<?php

declare(strict_types=1);

require __DIR__ . '/config.php';

try {
    $pdo = db();
    $sources = $pdo->query('SELECT id, name, url FROM sources WHERE active = 1 ORDER BY id ASC')->fetchAll();

    $insert = $pdo->prepare(
    'INSERT OR IGNORE INTO items (source_id, guid, title, link, description, pub_date)
     VALUES (:source_id, :guid, :title, :link, :description, :pub_date)'
);

$summary = [];

foreach ($sources as $source) {
    $url = $source['url'];
    $count = 0;

    libxml_use_internal_errors(true);
    $xml = @simplexml_load_file($url, 'SimpleXMLElement', LIBXML_NOCDATA);

    if ($xml === false) {
        $summary[] = "[FAIL] {$source['name']}: cannot load feed";
        continue;
    }

    $entries = [];
    if (isset($xml->channel->item)) {
        $entries = $xml->channel->item;
    } elseif (isset($xml->entry)) {
        $entries = $xml->entry;
    }

    foreach ($entries as $entry) {
        $title = trim((string)($entry->title ?? 'Untitled'));
        $link = '';

        if (isset($entry->link)) {
            if ($entry->link->attributes()->href) {
                $link = (string)$entry->link->attributes()->href;
            } else {
                $link = (string)$entry->link;
            }
        }

        if ($link === '') {
            continue;
        }

        $guid = (string)($entry->guid ?? $entry->id ?? sha1($source['id'] . $link . $title));
        $description = (string)($entry->description ?? $entry->summary ?? '');
        $pubDate = (string)($entry->pubDate ?? $entry->updated ?? $entry->published ?? '');

        $insert->execute([
            ':source_id' => $source['id'],
            ':guid' => $guid,
            ':title' => $title,
            ':link' => $link,
            ':description' => $description,
            ':pub_date' => $pubDate,
        ]);

        if ($insert->rowCount() > 0) {
            $count++;
        }
    }

    $summary[] = "[OK] {$source['name']}: {$count} new items";
}

header('Content-Type: text/plain; charset=UTF-8');
echo "RSS Studio cron run\n";
echo implode("\n", $summary);
echo "\nDone.\n";
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');
    echo 'ERROR: ' . $e->getMessage() . "\n";
}
