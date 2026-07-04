<?php
$directories = [
    'app/Enums',
    'app/Repositories',
    'app/Repositories/Contracts',
    'app/Services',
    'app/DTOs',
];

foreach ($directories as $dir) {
    $path = __DIR__ . '/' . $dir;
    if (!is_dir($path)) {
        mkdir($path, 0755, true);
        echo "Created: $dir\n";
    } else {
        echo "Exists: $dir\n";
    }
}

echo "\nAll directories ready!\n";
