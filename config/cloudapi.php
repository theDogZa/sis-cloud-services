<?php

return [

    'api'=> [
        'get_token' => [
            'name' => 'get Token',
            'des' => '',
            'methods' => '',
            'url' => '/api/sessions',
            'accept' => 'application/*;version=34.0',
            'host' => '',
        ],

        'get_url_by_cuscode' => [
            'name' => 'get url by Organization',
            'des' => '',
            'methods' => '',
            'url' => '/api/org',
            'accept' => 'application/*;version=34.0',
            'host' => '',
        ],

        'get_url_by_vdcrollup' => [
            'name' => 'get url by vdcRollup',
            'des' => '',
            'methods' => '',
            'url' => '',
            'accept' => 'application/*;version=34.0',
            'host' => '',
        ],

        'get_url_by_OVDC' => [
            'name' => 'get url by OVDC',
            'orgCode' => '-OVDC',
            'des' => '',
            'methods' => '',
            'url' => '',
            'accept' => 'application/*;version=34.0',
            'host' => '',
        ],

        'get_url_by_network_profile' => [
            'name' => 'get url by Network Profile',
            'des' => '',
            'methods' => '',
            'url' => '',
            'accept' => 'application/*;version=34.0',
            'host' => '',
        ],

        'get_primary_edge_cluster' => [
            'name' => 'get PrimaryEdgeCluster',
            'des' => '',
            'methods' => '',
            'url' => '',
            'accept' => 'application/*;version=34.0',
            'host' => '',
        ],

        'put_primary_edge_cluster' => [
            'name' => 'put PrimaryEdgeCluster',
            'des' => '',
            'methods' => '',
            'url' => '',
            'accept' => 'application/*;version=34.0',
            'contentType' => 33495,
            'contentLength' => 'application/json',
            'host' => '',
            'cluster_id' => 'urn:vcloud:edgeCluster:e68dadb5-a125-4c7d-8b20-d87e4848e3c3'
        ],

        'get_chk_primary_edge_cluster' => [
            'name' => 'get Check PrimaryEdgeCluster',
            'des' => '',
            'methods' => '',
            'url' => '',
            'accept' => 'application/*;version=34.0',
            'host' => '',
        ],
    ]


];
