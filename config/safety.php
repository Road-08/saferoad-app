<?php

return [
    'weights' => [
        'hazard' => 0.30,
        'lighting' => 0.25,
        'cctv' => 0.20,
        'security_post' => 0.15,
        'crowd' => 0.10,
    ],

    'radii_meters' => [
        'cctv' => 50,
        'lighting' => 40,
        'security_post_max_distance' => 500,
        'hazard' => 75,
    ],

    'sample_spacing_m' => 25,
    'max_samples' => 400,
    'min_available_factors' => 3,

    'thresholds' => [
        'aman' => 80,
        'sedang' => 60,
    ],

    'hazard_weights' => [
        'pelecehan' => 1.5,
        'kecelakaan' => 1.0,
        'default' => 0.8,
    ],
    'recency_half_life_days' => 180,
    'recency_min_factor' => 0.2,
    'hazard_penalty_per_unit' => 25,

    'limits' => [
        'max_routes' => 3,
        'max_points_per_route' => 1000,
    ],

    'bbox_margin_m' => 150,

    'crowd' => [
        'min_samples' => 30,
    ],
];
