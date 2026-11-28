<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Bobot Komponen Utama Safety Score (Total: 100%)
    |--------------------------------------------------------------------------
    | 1. Kriminalitas & Risiko Kejadian : 50%
    | 2. Penerangan Jalan              : 20%
    | 3. Fasilitas Keamanan (CCTV/Pos) : 15%
    | 4. Aktivitas Lingkungan (Keramaian): 15%
    */
    'components' => [
        'crime_weight' => 0.50,
        'lighting_weight' => 0.20,
        'facility_weight' => 0.15,
        'activity_weight' => 0.15,
    ],

    /*
    |--------------------------------------------------------------------------
    | Bobot Status Pemeriksaan Laporan (Risk Contribution Multiplier)
    |--------------------------------------------------------------------------
    | - waiting_verification : Laporan baru indikasi awal (bobot 35% risiko)
    | - under_investigation  : Sedang dicek lapangan oleh petugas (bobot 50%)
    | - verified_true        : Kejadian dipastikan benar (bobot penuh 100%)
    | - handled              : Sudah ditangani petugas (risiko turun drastis, sisa 15%)
    | - unproven             : Laporan tidak terbukti (risiko dihapus / 0%)
    | - safe_condition       : Penanganan tuntas dan kondisi dipastikan aman (0%)
    */
    'inspection_weights' => [
        'waiting_verification' => 0.35,
        'under_investigation' => 0.50,
        'verified_true' => 1.00,
        'handled' => 0.15,
        'unproven' => 0.00,
        'safe_condition' => 0.00,
    ],

    /*
    |--------------------------------------------------------------------------
    | Multiplier Tingkat Keparahan Laporan (Severity Multipliers)
    |--------------------------------------------------------------------------
    */
    'severity_multipliers' => [
        'tinggi' => 1.5,
        'sedang' => 1.0,
        'rendah' => 0.6,
    ],

    /*
    |--------------------------------------------------------------------------
    | Base Penalty Berdasarkan Kategori Kejadian (Poin Pengurang dari 100)
    |--------------------------------------------------------------------------
    */
    'incident_penalties' => [
        'harassment' => 28,     // Pelecehan seksual / asusila
        'robbery' => 26,        // Perampokan / begal / penodongan
        'intimidation' => 24,   // Intimidasi / kekerasan fisik
        'theft' => 18,          // Pencurian / maling
        'catcalling' => 18,     // Catcalling / siulan / gangguan verbal
        'road_condition' => 12, // Jalan rusak / lubang
        'facility' => 10,       // Fasilitas keamanan rusak
        'other' => 14,          // Kategori lainnya
    ],

    /*
    |--------------------------------------------------------------------------
    | Bobot Usia Laporan (Recency Decay)
    |--------------------------------------------------------------------------
    */
    'recency' => [
        'fresh_days' => 7,
        'fresh_weight' => 1.0,
        'recent_days' => 30,
        'recent_weight' => 0.80,
        'older_weight' => 0.50,
    ],

    /*
    |--------------------------------------------------------------------------
    | Bobot Jarak Terhadap Rute / Titik (Distance Decay)
    |--------------------------------------------------------------------------
    */
    'distance' => [
        'immediate_km' => 0.15, // <= 150 meter: 100% dampak
        'immediate_weight' => 1.0,
        'medium_km' => 0.25,    // <= 250 meter: 85% dampak
        'medium_weight' => 0.85,
        'outer_km' => 0.40,     // <= 400 meter: 65% dampak
        'outer_weight' => 0.65,
    ],

    /*
    |--------------------------------------------------------------------------
    | Komponen Penerangan Jalan
    |--------------------------------------------------------------------------
    */
    'lighting' => [
        'optimal_lights_count' => 3, // Skor 100
        'moderate_lights_count' => 2, // Skor 80
        'minimal_lights_count' => 1,  // Skor 60
        'zero_lights_score' => 25,
        'damaged_penalty' => 12,
    ],

    /*
    |--------------------------------------------------------------------------
    | Komponen Fasilitas Keamanan (CCTV & Pos)
    |--------------------------------------------------------------------------
    */
    'facilities' => [
        'base_point' => 20,
        'cctv_point' => 40,
        'security_post_point' => 45,
    ],

    /*
    |--------------------------------------------------------------------------
    | Radius Default Analisis Area (Kilometer)
    |--------------------------------------------------------------------------
    */
    'default_area_radius_km' => 0.50, // 500 meter
];
