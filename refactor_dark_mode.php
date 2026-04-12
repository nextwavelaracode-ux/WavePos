<?php

$directory = __DIR__ . '/resources/views';

function scanAndReplace($dir) {
    $files = glob($dir . '/*');
    
    $replacementsClasses = [
        // TailAdmin specific legacy classes
        'dark:bg-boxdark' => 'dark:bg-neutral-900',
        'dark:border-strokedark' => 'dark:border-neutral-800/80',
        'dark:bg-meta-4' => 'dark:bg-neutral-800/20',
        'bg-gray-2 ' => 'bg-gray-50 ',
        'border-[#eee]' => 'border-gray-100',
        
        // Gray palette generic replacements
        'dark:bg-gray-900' => 'dark:bg-neutral-900',
        'dark:bg-gray-950' => 'dark:bg-neutral-950',
        'dark:bg-gray-800' => 'dark:bg-neutral-800',
        'dark:bg-gray-dark' => 'dark:bg-neutral-900',
        'dark:border-gray-800' => 'dark:border-neutral-800/80',
        'dark:border-white/[0.05]' => 'dark:border-neutral-800/80',
        'dark:bg-white/[0.03]' => 'dark:bg-neutral-800/20',
        'dark:bg-white/[0.05]' => 'dark:bg-neutral-800/30',
        'dark:hover:bg-white/[0.02]' => 'dark:hover:bg-neutral-800/10'
    ];

    foreach ($files as $file) {
        if (is_dir($file)) {
            scanAndReplace($file);
        } else if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            $content = file_get_contents($file);
            $originalContent = $content;

            foreach ($replacementsClasses as $old => $new) {
                $content = str_replace($old, $new, $content);
            }

            if ($content !== $originalContent) {
                file_put_contents($file, $content);
                echo "Updated: " . str_replace(__DIR__, '', $file) . "\n";
            }
        }
    }
}

echo "Starting rigorous replacement...\n";
scanAndReplace($directory);
echo "Completed.\n";
