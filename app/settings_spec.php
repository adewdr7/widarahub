<?php
// Kolom pengaturan website. store: 'village' (tabel villages) atau 'setting' (tabel site_settings)
return [
    'Identitas Desa' => [
        ['name' => 'name', 'store' => 'village', 'label' => 'Nama desa', 'type' => 'text', 'required' => true],
        ['name' => 'district', 'store' => 'village', 'label' => 'Kecamatan', 'type' => 'text'],
        ['name' => 'regency', 'store' => 'village', 'label' => 'Kabupaten', 'type' => 'text'],
        ['name' => 'province', 'store' => 'village', 'label' => 'Provinsi', 'type' => 'text'],
        ['name' => 'motto', 'store' => 'village', 'label' => 'Semboyan desa', 'type' => 'text'],
        ['name' => 'is_verified', 'store' => 'village', 'label' => 'Informasi desa sudah diverifikasi (menyembunyikan banner demo)', 'type' => 'checkbox'],
    ],
    'Kontak & Media Sosial' => [
        ['name' => 'address', 'store' => 'village', 'label' => 'Alamat kantor desa', 'type' => 'textarea'],
        ['name' => 'phone', 'store' => 'village', 'label' => 'Telepon', 'type' => 'text'],
        ['name' => 'email', 'store' => 'village', 'label' => 'Email', 'type' => 'text'],
        ['name' => 'whatsapp', 'store' => 'village', 'label' => 'WhatsApp (format 62812...)', 'type' => 'text'],
        ['name' => 'office_hours', 'store' => 'village', 'label' => 'Jam pelayanan', 'type' => 'textarea'],
        ['name' => 'maps_lat', 'store' => 'village', 'label' => 'Latitude kantor desa (peta)', 'type' => 'coord', 'range' => 90, 'hint' => 'Contoh: -6.94936. Kosongkan untuk menyembunyikan peta.'],
        ['name' => 'maps_lng', 'store' => 'village', 'label' => 'Longitude kantor desa (peta)', 'type' => 'coord', 'range' => 180, 'hint' => 'Contoh: 108.52098'],
        ['name' => 'facebook', 'store' => 'village', 'label' => 'Facebook (URL)', 'type' => 'text'],
        ['name' => 'instagram', 'store' => 'village', 'label' => 'Instagram (URL)', 'type' => 'text'],
        ['name' => 'youtube', 'store' => 'village', 'label' => 'YouTube (URL)', 'type' => 'text'],
    ],
    'Beranda' => [
        ['name' => 'hero_title', 'store' => 'setting', 'label' => 'Judul hero', 'type' => 'text'],
        ['name' => 'hero_subtitle', 'store' => 'setting', 'label' => 'Deskripsi hero', 'type' => 'textarea'],
        ['name' => 'hero_image', 'store' => 'setting', 'label' => 'Foto hero', 'type' => 'image', 'hint' => '1920 × 1080 px (16:9), maks. ±300 KB'],
        ['name' => 'sambutan_name', 'store' => 'setting', 'label' => 'Nama Kepala Desa', 'type' => 'text'],
        ['name' => 'sambutan_position', 'store' => 'setting', 'label' => 'Jabatan', 'type' => 'text'],
        ['name' => 'sambutan_text', 'store' => 'setting', 'label' => 'Teks sambutan', 'type' => 'textarea'],
        ['name' => 'sambutan_photo', 'store' => 'setting', 'label' => 'Foto Kepala Desa', 'type' => 'image', 'hint' => '600 × 750 px (rasio 4:5)'],
    ],
    'Lainnya' => [
        ['name' => 'org_chart_image', 'store' => 'setting', 'label' => 'Bagan struktur organisasi', 'type' => 'image', 'hint' => '1600 × 1000 px, PNG/JPG'],
        ['name' => 'footer_description', 'store' => 'setting', 'label' => 'Deskripsi di footer', 'type' => 'textarea'],
    ],
];
