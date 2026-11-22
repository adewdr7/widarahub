<?php
function db(): PDO
{
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $path = config('db_path');
    if (!is_dir(dirname($path))) mkdir(dirname($path), 0775, true);

    $pdo = new PDO('sqlite:' . $path);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA foreign_keys = ON');

    $has = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='users'")->fetchColumn();
    if (!$has) Installer::run($pdo);   // instalasi otomatis saat pertama dijalankan
    $fin = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='village_budgets'")->fetchColumn();
    if (!$fin) Installer::migrateKeuangan();   // migrasi otomatis modul keuangan (data lama aman)
    $sdm = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='trainings'")->fetchColumn();
    if (!$sdm) Installer::migrateSdm();        // migrasi otomatis pelatihan, minat warga, kolom UMKM
    $hasMap = false;
    foreach ($pdo->query('PRAGMA table_info(villages)') as $col) if ($col['name'] === 'maps_lat') $hasMap = true;
    if (!$hasMap) Installer::migrateMap();     // migrasi otomatis koordinat peta
    return $pdo;
}

function db_exec(string $sql, array $p = []): PDOStatement
{
    $s = db()->prepare($sql);
    $s->execute($p);
    return $s;
}
function db_all(string $sql, array $p = []): array { return db_exec($sql, $p)->fetchAll(); }
function db_one(string $sql, array $p = []): ?array { $r = db_exec($sql, $p)->fetch(); return $r ?: null; }
function db_val(string $sql, array $p = []) { return db_exec($sql, $p)->fetchColumn(); }

function db_insert(string $table, array $data): int
{
    $cols = array_keys($data);
    $sql = 'INSERT INTO ' . $table . ' (' . implode(',', $cols) . ') VALUES (' . implode(',', array_fill(0, count($cols), '?')) . ')';
    db_exec($sql, array_values($data));
    return (int)db()->lastInsertId();
}

function db_update(string $table, int $id, array $data): void
{
    $set = implode(',', array_map(function ($c) { return $c . ' = ?'; }, array_keys($data)));
    $params = array_values($data);
    $params[] = $id;
    db_exec('UPDATE ' . $table . ' SET ' . $set . ' WHERE id = ?', $params);
}
