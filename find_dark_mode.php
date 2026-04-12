<?php
$dirs = ['resources/views/pages', 'resources/views/components'];
$filesToFix = [];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) continue;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
            $content = file_get_contents($file->getPathname());
            // Find files that have 'bg-white' but lack matching 'dark:bg-...' nearby, or simply files that need dark mode
            if (strpos($content, 'bg-white') !== false && strpos($content, 'dark:bg-') === false) {
                // To be more precise, let's just count how many bg-white exist vs dark:bg
                $bgWhiteCount = substr_count($content, 'bg-white');
                $darkBgCount = substr_count($content, 'dark:bg-') + substr_count($content, 'dark:bg-neutral-') + substr_count($content, 'dark:bg-gray-');
                
                if ($bgWhiteCount > $darkBgCount) {
                    $filesToFix[] = $file->getPathname();
                }
            } else {
                // If it contains bg-white, but might have some dark:bg- but not enough
                $bgWhiteCount = substr_count($content, 'bg-white');
                $darkBgCount = substr_count($content, 'dark:bg-');
                // Allow some tolerance, but if there's a big discrepancy...
                if ($bgWhiteCount > 0 && ($bgWhiteCount - $darkBgCount > 1)) {
                    $filesToFix[] = $file->getPathname();
                }
            }
        }
    }
}
$filesToFix = array_unique($filesToFix);
file_put_contents('missing_dark.txt', implode("\n", $filesToFix));
echo "Saved to missing_dark.txt";
