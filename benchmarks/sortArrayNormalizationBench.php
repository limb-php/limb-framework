<?php
/**
 * Microbenchmark: lmbArrayHelper::sortArray() normalisation overhead.
 *
 * Compares:
 *   - old:   no shape-normalisation (pre-fix: ['id'] silently broke on object rows)
 *   - new:   normalises loose forms in-place (current implementation)
 *
 * Usage:
 *   C:\php-8.2\php.exe benchmarks/sortArrayNormalizationBench.php
 *   C:\php-8.2\php.exe benchmarks/sortArrayNormalizationBench.php --iterations=20 --sizes=1000,10000,50000
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use limb\core\src\lmbObject;

// ---------------------------------------------------------------------------
// Implementations under test
// ---------------------------------------------------------------------------

/**
 * Pre-fix implementation: no normalisation. Faithful to what was in the tree
 * before today's change (eval is already gone, kept splat version for fairness).
 *
 * @throws \Throwable when passed something the old code couldn't handle, e.g.
 *                   ['id'] over lmbObject rows — that's the bug we fixed.
 */
function sortArrayOld(array &$array, $sort_params, bool $preserve_keys = true): bool
{
    $array_mod = [];
    foreach ($array as $key => $value) {
        $array_mod['_' . $key] = $value;
    }

    $sort_values = [];
    $sort_flags = [];
    $args = [];
    $i = 0;
    // No normalisation: int-keyed values and strings flow straight through,
    // which is exactly why the regression existed.
    foreach ((array) $sort_params as $name => $sort_type) {
        $column = [];
        foreach ($array_mod as $row) {
            if (is_object($row)) {
                $column[] = $row->get($name); // explodes when $name === 0
            } else {
                $column[] = $row[$name] ?? null;
            }
        }
        $sort_values[$i] = $column;
        $sort_flags[$i] = ($sort_type === 'DESC') ? SORT_DESC : SORT_ASC;
        $args[] = &$sort_values[$i];
        $args[] = &$sort_flags[$i];
        $i++;
    }
    $args[] = &$array_mod;

    if ($args) {
        array_multisort(...$args);
    }

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

/** Post-fix implementation: just defers to the real shipped code. */
function sortArrayNew(array &$array, $sort_params, bool $preserve_keys = true): bool
{
    \limb\core\src\lmbArrayHelper::sortArray($array, $sort_params, $preserve_keys);
    return true;
}

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function makeAssocDataset(int $rows, int $seed = 1): array
{
    mt_srand($seed);
    $out = [];
    for ($i = 0; $i < $rows; $i++) {
        $out[] = ['id' => mt_rand(1, 1_000_000), 'a' => mt_rand(0, 100), 'b' => mt_rand(0, 1000)];
    }
    return $out;
}

function makeObjectDataset(int $rows, int $seed = 1): array
{
    mt_srand($seed);
    $out = [];
    for ($i = 0; $i < $rows; $i++) {
        $out[] = new lmbObject(['id' => mt_rand(1, 1_000_000), 'a' => mt_rand(0, 100)]);
    }
    return $out;
}

function measure(callable $fn, array $dataset, $params, int $iterations): array
{
    gc_collect_cycles();
    memory_reset_peak_usage();
    $mem0 = memory_get_usage();

    $t0 = hrtime(true);
    for ($i = 0; $i < $iterations; $i++) {
        $copy = $dataset;
        $fn($copy, $params, true);
    }
    $elapsed_ns = hrtime(true) - $t0;

    return [
        'elapsed_ms' => $elapsed_ns / 1_000_000,
        'per_call_ms' => ($elapsed_ns / 1_000_000) / max(1, $iterations),
        'peak_delta' => max(0, memory_get_peak_usage() - $mem0),
    ];
}

function fmtBytes(int $b): string
{
    if ($b < 1024) return $b . ' B';
    if ($b < 1024 * 1024) return sprintf('%.1f KiB', $b / 1024);
    return sprintf('%.2f MiB', $b / 1024 / 1024);
}

function parseArgs(array $argv): array
{
    $opts = [
        'iterations' => 10,
        'sizes' => [1000, 10_000, 50_000],
    ];
    foreach (array_slice($argv, 1) as $arg) {
        if (preg_match('/^--iterations=(\d+)$/', $arg, $m)) $opts['iterations'] = (int) $m[1];
        elseif (preg_match('/^--sizes=([\d,]+)$/', $arg, $m)) $opts['sizes'] = array_map('intval', explode(',', $m[1]));
    }
    return $opts;
}

function runRow(string $label, string $rows_label, int $rows, array $dataset, $params, int $iterations): void
{
    $old = measure('sortArrayOld', $dataset, $params, $iterations);
    $new = measure('sortArrayNew', $dataset, $params, $iterations);

    $overhead = $new['per_call_ms'] - $old['per_call_ms'];
    $overhead_pct = $old['per_call_ms'] > 0 ? ($overhead / $old['per_call_ms']) * 100 : 0;

    printf(
        "%-28s %8d %-14s %12.4f %12.4f %10.4f %9.2f%%\n",
        $label,
        $rows,
        $rows_label,
        $old['per_call_ms'],
        $new['per_call_ms'],
        $overhead,
        $overhead_pct
    );
}

// ---------------------------------------------------------------------------
// Main
// ---------------------------------------------------------------------------

$opts = parseArgs($argv);
$iterations = $opts['iterations'];
$sizes = $opts['sizes'];

echo "PHP ", PHP_VERSION, "  |  iterations per cell: {$iterations}\n";
echo str_repeat('=', 104), "\n";

// ======================================================================
// Part 1 — Normalisation overhead on the happy path.
// The input is already canonical, so normalisation should be near-free.
// ======================================================================
echo "\n[1] Overhead on CANONICAL input (['a'=>'DESC','b'=>'ASC']) — assoc rows\n";
echo str_repeat('-', 104), "\n";
printf("%-28s %8s %-14s %12s %12s %10s %9s\n",
    'scenario', 'rows', 'shape', 'old ms/call', 'new ms/call', 'Δ ms', 'overhead');
echo str_repeat('-', 104), "\n";

foreach ($sizes as $rows) {
    $ds = makeAssocDataset($rows);
    runRow('canonical 2-col', 'assoc', $rows, $ds, ['a' => 'DESC', 'b' => 'ASC'], $iterations);
}

// ======================================================================
// Part 2 — Loose shapes where new has extra work to do.
// ======================================================================
echo "\n[2] LOOSE input that requires normalisation (['a','b'=>'DESC']) — assoc rows\n";
echo str_repeat('-', 104), "\n";
printf("%-28s %8s %-14s %12s %12s %10s %9s\n",
    'scenario', 'rows', 'shape', 'old ms/call', 'new ms/call', 'Δ ms', 'overhead');
echo str_repeat('-', 104), "\n";

foreach ($sizes as $rows) {
    $ds = makeAssocDataset($rows);
    runRow('loose int-keyed', 'assoc', $rows, $ds, ['a', 'b' => 'DESC'], $iterations);
}

echo "\n[3] String param ('a') — old would treat as canonical-but-broken, new normalises\n";
echo str_repeat('-', 104), "\n";
printf("%-28s %8s %-14s %12s %12s %10s %9s\n",
    'scenario', 'rows', 'shape', 'old ms/call', 'new ms/call', 'Δ ms', 'overhead');
echo str_repeat('-', 104), "\n";

foreach ($sizes as $rows) {
    $ds = makeAssocDataset($rows);
    runRow('string param', 'assoc', $rows, $ds, 'a', $iterations);
}

// ======================================================================
// Part 3 — The actual regression: ['id'] over lmbObject rows.
// Old code blows up; new code sorts correctly.
// ======================================================================
echo "\n[4] REGRESSION CASE — ['id'] over lmbObject rows (old throws, new sorts)\n";
echo str_repeat('-', 104), "\n";
printf("%-28s %8s %-24s %-24s\n", 'scenario', 'rows', 'old behaviour', 'new behaviour');
echo str_repeat('-', 104), "\n";

foreach ([100, 1000, 10_000] as $rows) {
    $ds = makeObjectDataset($rows);

    // Probe old behaviour once.
    $old_behaviour = '';
    try {
        $copy = $ds;
        sortArrayOld($copy, ['id'], false);
        $old_behaviour = 'silently wrong order';
    } catch (\Throwable $e) {
        $old_behaviour = 'throws ' . (new \ReflectionClass($e))->getShortName();
    }

    // New implementation: should succeed and produce a sorted set.
    $new_behaviour = '';
    try {
        $copy = $ds;
        sortArrayNew($copy, ['id'], false);
        $sorted = true;
        for ($i = 1; $i < count($copy); $i++) {
            if ($copy[$i]->get('id') < $copy[$i - 1]->get('id')) {
                $sorted = false;
                break;
            }
        }
        $new_behaviour = $sorted ? 'sorted ASC' : 'ran but not sorted';
    } catch (\Throwable $e) {
        $new_behaviour = 'throws ' . (new \ReflectionClass($e))->getShortName();
    }

    printf("%-28s %8d %-24s %-24s\n", "['id'] on lmbObject rows", $rows, $old_behaviour, $new_behaviour);
}

// ======================================================================
// Part 4 — Micro-cost of the normaliser alone.
// ======================================================================
echo "\n[5] Cost of the normaliser ALONE on the various shapes (million calls)\n";
echo str_repeat('-', 104), "\n";
printf("%-32s %12s %12s %14s\n", 'input', 'calls', 'total ms', 'ns/call');
echo str_repeat('-', 104), "\n";

$shapes = [
    'canonical'    => ['a' => 'ASC', 'b' => 'DESC', 'c' => 'ASC'],
    'int-keyed'    => ['a', 'b', 'c'],
    'mixed'        => ['a', 'b' => 'DESC'],
    'string'       => 'id',
    'empty'        => [],
];

$million = 1_000_000;
foreach ($shapes as $label => $shape) {
    $t0 = hrtime(true);
    for ($i = 0; $i < $million; $i++) {
        $arr = []; // empty to isolate normaliser cost: real sort is a no-op
        sortArrayNew($arr, $shape);
    }
    $ns = hrtime(true) - $t0;
    printf("%-32s %12s %12.2f %14.1f\n", $label, number_format($million), $ns / 1_000_000, $ns / $million);
}
