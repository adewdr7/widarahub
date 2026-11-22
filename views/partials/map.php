<?php
/* Peta OpenStreetMap (Leaflet). Variabel: $markers = [['lat'=>float,'lng'=>float,'title'=>string,'url'=>string(opsional)], ...], $label (opsional)
   Butuh koneksi internet untuk memuat Leaflet dan ubin peta. Tanpa API key. */
$markers = array_values(array_filter($markers ?? [], function ($m) { return is_numeric($m['lat'] ?? null) && is_numeric($m['lng'] ?? null); }));
if ($markers):
    foreach ($markers as &$__m) { $__m['lat'] = (float)$__m['lat']; $__m['lng'] = (float)$__m['lng']; }
    unset($__m);
    if (empty($GLOBALS['_leaflet_loaded'])): $GLOBALS['_leaflet_loaded'] = true; ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" crossorigin="">
<link rel="stylesheet" href="<?= asset('css/peta.css') ?>">
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js" crossorigin="" defer></script>
<script src="<?= asset('js/map.js') ?>" defer></script>
<?php endif; ?>
<div class="map" data-map data-markers="<?= e(json_encode($markers, JSON_UNESCAPED_UNICODE)) ?>" role="region" aria-label="<?= e($label ?? 'Peta lokasi') ?>"></div>
<?php endif; ?>
