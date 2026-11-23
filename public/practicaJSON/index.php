<?php
$productos = [
    [
        "id" => 1,
        "nombre" => "Mouse inalámbrico",
        "precio" => 35000,
        "stock" => 12,
        "disponible" => true
    ],
    [
        "id" => 2,
        "nombre" => "Teclado mecánico",
        "precio" => 165000,
        "stock" => 8,
        "disponible" => true
    ],
    [
        "id" => 3,
        "nombre" => "Monitor de 24 pulgadas",
        "precio" => 780000,
        "stock" => 6,
        "disponible" => true
    ]
];

$productos[] = [
    "id" => 4,
    "nombre" => "Cable USB-C",
    "precio" => 25000,
    "stock" => 30,
    "disponible" => true
];

echo json_encode($productos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
