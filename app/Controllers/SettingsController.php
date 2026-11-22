<?php
class SettingsController
{
    private static function spec(): array { return require BASE_PATH . '/app/settings_spec.php'; }

    public static function index(array $errors = [], ?array $posted = null): void
    {
        Auth::need('settings.view');
        $values = village();
        foreach (self::spec() as $fields) {
            foreach ($fields as $f) if ($f['store'] === 'setting') $values[$f['name']] = setting($f['name'], '');
        }
        if ($posted) $values = array_merge($values, $posted);
        view('admin/settings', ['title' => 'Pengaturan Website', 'groups' => self::spec(), 'values' => $values, 'errors' => $errors], 'admin');
    }

    public static function save(): void
    {
        Auth::need('settings.edit');
        csrf_check();
        $village = [];
        $settings = [];
        $errors = [];

        foreach (self::spec() as $fields) {
            foreach ($fields as $f) {
                $n = $f['name'];
                $raw = $_POST[$n] ?? '';
                $val = is_string($raw) ? trim($raw) : '';
                if ($f['type'] === 'checkbox') $val = isset($_POST[$n]) ? 1 : 0;
                if ($f['type'] === 'coord') {
                    $val = str_replace(',', '.', $val);
                    if ($val !== '' && (!is_numeric($val) || abs((float)$val) > $f['range'])) { $errors[$n] = 'Koordinat tidak valid.'; continue; }
                    $val = $val === '' ? null : (float)$val;
                }
                if ($f['type'] === 'image') {
                    [$path, $err] = Uploader::handle($n, 'image', 'pengaturan');
                    if ($err) { $errors[$n] = $err; continue; }
                    if ($path === null) continue;      // tidak mengganti gambar
                    $val = $path;
                }
                if (!empty($f['required']) && $val === '') { $errors[$n] = $f['label'] . ' wajib diisi.'; continue; }
                if ($f['store'] === 'village') $village[$n] = $val; else $settings[$n] = (string)$val;
            }
        }
        if ($errors) { self::index($errors, $_POST); return; }

        $village['updated_at'] = now();
        db_update('villages', 1, $village);
        foreach ($settings as $k => $v) {
            db_exec('INSERT OR REPLACE INTO site_settings (setting_key, setting_value) VALUES (?, ?)', [$k, $v]);
        }
        audit('update', 'site_settings', null, 'pengaturan website');
        flash('success', 'Pengaturan disimpan.');
        redirect('/admin/settings');
    }
}
