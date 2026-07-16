<?php

return [

    'disk' => 'public',

    'directory' => 'media',

    'max_upload_kb' => (int) env('MEDIA_MAX_UPLOAD_KB', 10240),

    'allowed_mimes' => [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'webp' => 'image/webp',
        'svg' => 'image/svg+xml',
        'pdf' => 'application/pdf',
        'mp4' => 'video/mp4',
        'webm' => 'video/webm',
        'mov' => 'video/quicktime',
    ],

    'thumbnail' => [
        'enabled' => true,
        'max_width' => 400,
        'max_height' => 400,
        'directory' => 'media/thumbnails',
    ],

];
