<?php
$arUrlRewrite=array (
    0 =>
        array(
            'CONDITION' => '#^(.*)#',
            'PATH' => '/app/index.php'
        ),
    1 =>
        array(
            'CONDITION' => '#^/api/([a-zA-Z0-9.]+)/?($|index.*|\\?.*)#',
            'RULE' => 'method=$1',
            'ID' => '',
            'PATH' => '/local/api/index.php',
            'SORT' => 100,
        )
);
