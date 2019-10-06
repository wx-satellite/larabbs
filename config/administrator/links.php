<?php

return [
    "title" => "资源推荐",
    "single" => "资源推荐",
    "model" => \App\Models\Link::class,
    "permission" => function() {
        return \Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->hasRole("Founder");
    },
    "columns" => [
        "id" => [
            "title" => "ID"
        ],
        "title" => [
            "title" => "资源描述",
            "sortable" => false,
        ],
        "link" => [
            "title" => "资源链接",
            "sortable" => false,
        ],
        "operation" => [
            "title" => "管理",
            "sortable" => false,
        ]
    ],
    "edit_fields" => [
        "title" => [
            "title" => "资源描述",
        ],
        "link" => [
            "title" => "资源链接"
        ],
    ],
    "filters" => [
        "id" => [
            "title" => "ID"
        ],
        "title" => [
            "title" => "资源名称"
        ]
    ]

];