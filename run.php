<?php


$totalcount = 0;
$modulecount = [];
$res = [];
$modules = [];

$vendors = ['silverstripe', 'cwp', 'symbiote', 'dnadesign'];

foreach ($vendors as $vendor) {
    $basepath = dirname(dirname(__DIR__)) . "/$vendor";
    $files = shell_exec('find ' . $basepath . ' -name "*.php"');
    foreach (explode("\n", $files) as $file) {
        if (!$file) continue;
        if (str_contains($file, '/tests/')) continue;
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
            if (!$s) continue;
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
            if ($module == 'graphql') continue;
            if ($module == 'graphql') continue;
            if ($module == 'graphql') continue;
            $res[$f] = $c;
            $modules[$module] ??= 0;
            $modules[$module] += $c;
            $totalcount += $c;
            break;
        }
    }
}

print_r($res);
print_r($modules);
echo "Total: $totalcount\n";
