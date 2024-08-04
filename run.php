<?php

echo "\n\$url_handlers:\n\n";

$totalcount = 0;
$modules = [];

$vendors = ['silverstripe', 'cwp', 'symbiote', 'dnadesign', 'bringyourownideas', 'colymba', 'tractorcow'];

foreach ($vendors as $vendor) {
    $basepath = dirname(dirname(__DIR__)) . "/$vendor";
    $files = shell_exec('find ' . $basepath . ' -name "*.php"');
    foreach (explode("\n", $files) as $file) {
        if (!$file) {
            continue;
        }
        if (str_contains($file, '/tests/')) {
            continue;
        }
        $contents = file_get_contents($file);
        $starts = [
            '    private static $url_handlers = [',
            '    private static array $url_handlers = [',
            '    private static $url_handlers = array(',
            '    private static array $url_handlers = array(',
        ];
        $ends = [
            '    ];',
            '    ];',
            '    );',
            '    );',
        ];
        for ($i = 0; $i < count($starts); $i++) {
            $start = $starts[$i];
            $end = $ends[$i];
            $s = strpos($contents, $start);
            if (!$s) {
                continue;
            }
            $e = strpos($contents, $end, $s);
            $substr = substr($contents, $s, $e - $s);
            $lines = explode("\n", $substr);
            $c = 0;
            foreach ($lines as $line) {
                if (str_contains($line, '=>')) {
                    $c++;
                }
            }
            $f = str_replace($basepath, '', $file);
            preg_match('#^/([a-zA-Z\-]+)/#', $f, $matches);
            $module = $matches[1];
            if (in_array($module, ['graphql', 'graphql-devtools'])) {
                continue;
            }
            $modules[$module] ??= 0;
            $modules[$module] += $c;
            $totalcount += $c;
            break;
        }
    }
}

asort($modules);
$modules = array_reverse($modules);
foreach ($modules as $name => $count) {
    echo "$name: $count\n";
}
echo "\nTotal: $totalcount\n";


echo "\n\nGraphQL:\n\n";

$totalcount = 0;
$modules = [];

$vendors = ['silverstripe', 'cwp', 'symbiote', 'dnadesign', 'bringyourownideas', 'colymba', 'tractorcow'];

foreach ($vendors as $vendor) {
    $basepath = dirname(dirname(__DIR__)) . "/$vendor";
    foreach (['Query', 'Mutation'] as $word) {
        $files = shell_exec('find ' . $basepath . ' -name "*' . $word . '.js"');
        foreach (explode("\n", $files) as $file) {
            if (!$file || !str_contains($file, '/state/')) {
                continue;
            }
            $f = str_replace($basepath, '', $file);
            preg_match('#^/([a-zA-Z\-]+)/#', $f, $matches);
            $module = $matches[1];
            $modules[$module] ??= 0;
            $modules[$module]++;
            $totalcount++;
        }
    }
}

asort($modules);
$modules = array_reverse($modules);
foreach ($modules as $name => $count) {
    echo "$name: $count\n";
}
echo "\nTotal: $totalcount\n";