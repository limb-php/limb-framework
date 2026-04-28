<?php
/**
 * Microbenchmark: lmbArrayHelper::sortArray() — old (eval) vs. new (splat).
 *
 * Usage:
 *   C:\php-8.2\php.exe benchmarks/sortArrayBench.php
 *   C:\php-8.2\php.exe benchmarks/sortArrayBench.php --iterations=5 --sizes=1000,10000,50000
 *
 * The two implementations are inlined below so we can compare them without
 * git-checking-out the old code. The "new" version is byte-equivalent to the
 * one currently living in src/limb/core/src/lmbArrayHelper.php.
 */

declare(strict_types=1);

// ---------------------------------------------------------------------------
// Implementations under test
// ---------------------------------------------------------------------------

/**
 * Pre-fix implementation: builds an array_multisort() call as source text
 * and runs it through eval().
 */
function sortArrayOld(array &$array, array $sort_params, bool $preserve_keys = true): bool
{
    $array_mod = [];
    foreach ($array as $key => $value) {
        $array_mod['_' . $key] = $value;
    }

    $sort_values = [];
    $sort_args = [];
    $i = 0;
    $multi_sort_line = 'return array_multisort( ';
    foreach ($sort_params as $name => $sort_type) {
        $i++;
        foreach ($array_mod as $row_key => $row) {
            if (is_object($row)) {
                $sort_values[$i][] = $row->get($name);
            } else {
                $sort_values[$i][] = $row[$name];
            }
        }

        $sort_args[$i] = ($sort_type === 'DESC') ? SORT_DESC : SORT_ASC;
        $multi_sort_line .= '$sort_values[' . $i . '], $sort_args[' . $i . '], ';
    }
    $multi_sort_line .= '$array_mod );';

    eval($multi_sort_line);

    $array = [];
    foreach ($array_mod as $key => $value) {
        if ($preserve_keys) {
            $array[substr($key, 1)] = $value;
        } else {
            $array[] = $value;
        }
    }

    return true;
}

/**
 * Post-fix implementation: no eval, pure splat into array_multisort().
 */
function sortArrayNew(array &$array, array $sort_params, bool $preserve_keys = true): bool
{
    $array_mod = [];
    foreach ($array as $key => $value) {
        $array_mod['_' . $key] = $value;
    }

    $sort_values = [];
    $sort_flags = [];
    $args = [];
    $i = 0;
    foreach ($sort_params as $name => $sort_type) {
        $column = [];
        foreach ($array_mod as $row) {
            if (is_object($row)) {
                $column[] = $row->get($name);
            } else {
                $column[] = $row[$name];
            }
        }
        $sort_values[$i] = $column;
        $sort_flags[$i] = ($sort_type === 'DESC') ? SORT_DESC : SORT_ASC;
        $args[] = &$sort_values[$i];
        $args[] = &$sort_flags[$i];
        $i++;
    }
    $args[] = &$array_mod;

    array_multisort(...$args);

    $array = [];
    foreach ($array_mod as $key => $value) {
        if ($preserve_keys) {
            $array[substr($key, 1)] = $value;
        } else {
            $array[] = $value;
        }
    }

    return true;
}

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function makeDataset(int $rows, int $seed = 1): array
{
    mt_srand($seed);
    $out = [];
    for ($i = 0; $i < $rows; $i++) {
        $out[] = [
            'a' => mt_rand(0, 100),
            'b' => mt_rand(0, 1000),
            'c' => mt_rand(0, 10),
            'name' => 'row_' . mt_rand(0, 9999),
        ];
    }
    return $out;
}

function measure(callable $fn, array $dataset, array $params, int $iterations): array
{
    gc_collect_cycles();
    $memBefore = memory_get_usage();
    $peakBefore = memory_get_peak_usage();
    memory_reset_peak_usage();

    $t0 = hrtime(true);
    $last = null;
    for ($i = 0; $i < $iterations; $i++) {
        $copy = $dataset;                // fresh array each iteration
        $fn($copy, $params, true);
        $last = $copy;                   // keep one materialized result for parity check
    }
    $elapsedNs = hrtime(true) - $t0;

    $peakAfter = memory_get_peak_usage();
    gc_collect_cycles();

    return [
        'elapsed_ms' => $elapsedNs / 1_000_000,
        'per_call_ms' => ($elapsedNs / 1_000_000) / max(1, $iterations),
        'peak_delta_bytes' => max(0, $peakAfter - $peakBefore),
        'result' => $last,
    ];
}

function fmtBytes(int $b): string
{
    if ($b < 1024) {
        return $b . ' B';
    }
    if ($b < 1024 * 1024) {
        return sprintf('%.1f KiB', $b / 1024);
    }
    return sprintf('%.2f MiB', $b / 1024 / 1024);
}

function parseArgs(array $argv): array
{
    $opts = [
        'iterations' => 5,
        'sizes' => [1000, 10_000, 50_000],
    ];
    foreach (array_slice($argv, 1) as $arg) {
        if (preg_match('/^--iterations=(\d+)$/', $arg, $m)) {
            $opts['iterations'] = (int) $m[1];
        } elseif (preg_match('/^--sizes=([\d,]+)$/', $arg, $m)) {
            $opts['sizes'] = array_map('intval', explode(',', $m[1]));
        }
    }
    return $opts;
}

// ---------------------------------------------------------------------------
// Main
// ---------------------------------------------------------------------------

$opts = parseArgs($argv);
$iterations = $opts['iterations'];
$sizes = $opts['sizes'];

$scenarios = [
    'single-key-asc'     => ['a' => 'ASC'],
    'single-key-desc'    => ['b' => 'DESC'],
    'two-keys-mixed'     => ['a' => 'DESC', 'b' => 'ASC'],
    'three-keys-mixed'   => ['c' => 'ASC', 'a' => 'DESC', 'b' => 'ASC'],
];

echo "PHP ", PHP_VERSION, "  |  iterations per cell: {$iterations}\n";
echo str_repeat('-', 92), "\n";
printf(
    "%-18s %8s %-19s %12s %12s %9s %13s\n",
    'scenario',
    'rows',
    'impl',
    'total_ms',
    'per_call_ms',
    'speedup',
    'peak_delta'
);
echo str_repeat('-', 92), "\n";

foreach ($sizes as $rows) {
    $dataset = makeDataset($rows);

    foreach ($scenarios as $label => $params) {
        $old = measure('sortArrayOld', $dataset, $params, $iterations);
        $new = measure('sortArrayNew', $dataset, $params, $iterations);

        // Correctness: both must produce an identical ordering.
        $equal = $old['result'] === $new['result'];

        $speedup = $new['per_call_ms'] > 0
            ? $old['per_call_ms'] / $new['per_call_ms']
            : INF;

        printf(
            "%-18s %8d %-19s %12.3f %12.4f %9s %13s\n",
            $label,
            $rows,
            'old (eval)',
            $old['elapsed_ms'],
            $old['per_call_ms'],
            '—',
            fmtBytes($old['peak_delta_bytes'])
        );
        printf(
            "%-18s %8s %-19s %12.3f %12.4f %8.2fx %13s  %s\n",
            '',
            '',
            'new (splat)',
            $new['elapsed_ms'],
            $new['per_call_ms'],
            $speedup,
            fmtBytes($new['peak_delta_bytes']),
            $equal ? '[results match]' : '[MISMATCH!]'
        );
    }
    echo str_repeat('-', 92), "\n";
}
