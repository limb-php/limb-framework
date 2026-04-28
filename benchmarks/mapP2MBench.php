<?php
/**
 * Microbenchmark: lmbObject $map_p2m cache — old (flat, unbounded) vs.
 * new (nested-by-class, bounded, per-class flushable).
 *
 * Usage:
 *   C:\php-8.2\php.exe benchmarks/mapP2MBench.php
 *   C:\php-8.2\php.exe benchmarks/mapP2MBench.php --iterations=5 --classes=20 --props=200
 *
 * Both implementations are inlined below (with their own static caches and
 * a minimal method_exists() lookup loop). That keeps the bench self-contained
 * — we don't need to check out the old code or toggle the real framework.
 */

declare(strict_types=1);

// ---------------------------------------------------------------------------
// Implementations under test
// ---------------------------------------------------------------------------

/**
 * Old style: flat cache keyed by "Class::property".
 * No cap, no per-class invalidation, grows forever.
 */
class OldBase
{
    public static array $cache = [];

    public static function clear(): void
    {
        self::$cache = [];
    }

    public function resolve(string $property): string|false
    {
        $hash = static::class . '::' . $property;
        if (array_key_exists($hash, self::$cache)) {
            return self::$cache[$hash];
        }

        $method = 'get' . ucfirst($property);
        if (method_exists($this, $method)) {
            self::$cache[$hash] = $method;
            return $method;
        }
        self::$cache[$hash] = false;
        return false;
    }
}

/**
 * New style: nested cache keyed by [class][property], with a soft cap
 * and a counter so we can flush on overflow without walking the structure.
 */
class NewBase
{
    public static array $cache = [];
    private static int $count = 0;
    private static int $limit = 10000;

    public static function clear(?string $class = null): void
    {
        if ($class === null) {
            self::$cache = [];
            self::$count = 0;
            return;
        }
        if (isset(self::$cache[$class])) {
            self::$count -= count(self::$cache[$class]);
            if (self::$count < 0) self::$count = 0;
            unset(self::$cache[$class]);
        }
    }

    public static function setLimit(int $limit): void
    {
        self::$limit = max(0, $limit);
    }

    public static function stats(): array
    {
        return ['entries' => self::$count, 'classes' => count(self::$cache), 'limit' => self::$limit];
    }

    public function resolve(string $property): string|false
    {
        $class = static::class;
        if (isset(self::$cache[$class]) && array_key_exists($property, self::$cache[$class])) {
            return self::$cache[$class][$property];
        }

        $method = 'get' . ucfirst($property);
        $result = method_exists($this, $method) ? $method : false;
        return self::remember($class, $property, $result);
    }

    private static function remember(string $class, string $property, string|false $method): string|false
    {
        if (self::$limit > 0 && self::$count >= self::$limit) {
            self::clear();
        }
        if (!isset(self::$cache[$class][$property])) {
            self::$count++;
        }
        self::$cache[$class][$property] = $method;
        return $method;
    }
}

// ---------------------------------------------------------------------------
// Test subject classes (50 of each, 3 real getters each).
// Generated once via a single declare block so this stays self-contained.
// ---------------------------------------------------------------------------

for ($i = 0; $i < 50; $i++) {
    eval("class OldSubject{$i} extends OldBase {
        function getFoo() { return 1; }
        function getBar() { return 2; }
        function getBaz() { return 3; }
    }");
    eval("class NewSubject{$i} extends NewBase {
        function getFoo() { return 1; }
        function getBar() { return 2; }
        function getBaz() { return 3; }
    }");
}

// ---------------------------------------------------------------------------
// Scenarios
// ---------------------------------------------------------------------------

/**
 * Warm-hit scenario: every resolve() is a cache hit.
 * Measures the cost of the hit-path itself.
 */
function scenarioWarmHits(string $impl, int $classes, int $lookups_per_class): int
{
    $prefix = $impl === 'old' ? 'OldSubject' : 'NewSubject';
    $clear = $impl === 'old' ? [OldBase::class, 'clear'] : [NewBase::class, 'clear'];

    $subjects = [];
    for ($c = 0; $c < $classes; $c++) {
        $cls = $prefix . $c;
        $subjects[] = new $cls();
    }

    $clear();
    // Warm up first
    foreach ($subjects as $s) {
        $s->resolve('foo');
        $s->resolve('bar');
        $s->resolve('baz');
        $s->resolve('missing_one');
    }

    $ops = 0;
    for ($i = 0; $i < $lookups_per_class; $i++) {
        foreach ($subjects as $s) {
            $s->resolve('foo');
            $s->resolve('bar');
            $s->resolve('baz');
            $s->resolve('missing_one');
            $ops += 4;
        }
    }
    return $ops;
}

/**
 * Cold-miss scenario: every resolve() is a first-time lookup.
 * Measures insertion-path cost (method_exists + cache write).
 */
function scenarioColdMisses(string $impl, int $classes, int $distinct_props): int
{
    $prefix = $impl === 'old' ? 'OldSubject' : 'NewSubject';
    $clear = $impl === 'old' ? [OldBase::class, 'clear'] : [NewBase::class, 'clear'];

    $subjects = [];
    for ($c = 0; $c < $classes; $c++) {
        $cls = $prefix . $c;
        $subjects[] = new $cls();
    }

    $clear();

    $ops = 0;
    foreach ($subjects as $s) {
        for ($p = 0; $p < $distinct_props; $p++) {
            $s->resolve('prop_' . $p); // all miss, all get cached as false
            $ops++;
        }
    }
    return $ops;
}

/**
 * Pathological unbounded-growth scenario: many distinct property names that
 * will never be hit again. The old cache grows until the process runs out
 * of memory; the new cache self-limits via the soft cap.
 */
function scenarioUnboundedGrowth(string $impl, int $classes, int $distinct_props): array
{
    $prefix = $impl === 'old' ? 'OldSubject' : 'NewSubject';
    $clear = $impl === 'old' ? [OldBase::class, 'clear'] : [NewBase::class, 'clear'];

    $subjects = [];
    for ($c = 0; $c < $classes; $c++) {
        $cls = $prefix . $c;
        $subjects[] = new $cls();
    }

    $clear();
    if ($impl === 'new') {
        NewBase::setLimit(10000); // same default as production
    }

    $mem0 = memory_get_usage();
    memory_reset_peak_usage();

    foreach ($subjects as $s) {
        for ($p = 0; $p < $distinct_props; $p++) {
            $s->resolve('dyn_' . $p . '_' . mt_rand(0, 1_000_000));
        }
    }

    $peak = memory_get_peak_usage() - $mem0;
    $entries = $impl === 'old' ? count(OldBase::$cache) : NewBase::stats()['entries'];

    return [
        'peak_bytes' => max(0, $peak),
        'entries' => $entries,
    ];
}

// ---------------------------------------------------------------------------
// Helpers
// ---------------------------------------------------------------------------

function measure(callable $fn, int $iterations): array
{
    gc_collect_cycles();
    memory_reset_peak_usage();
    $mem0 = memory_get_usage();

    $t0 = hrtime(true);
    $total_ops = 0;
    for ($i = 0; $i < $iterations; $i++) {
        $total_ops += (int) $fn();
    }
    $elapsed_ns = hrtime(true) - $t0;

    return [
        'elapsed_ms' => $elapsed_ns / 1_000_000,
        'ops' => $total_ops,
        'ns_per_op' => $total_ops > 0 ? $elapsed_ns / $total_ops : 0.0,
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
        'iterations' => 5,
        'classes' => 50,
        'props' => 100,
    ];
    foreach (array_slice($argv, 1) as $arg) {
        if (preg_match('/^--iterations=(\d+)$/', $arg, $m)) $opts['iterations'] = (int) $m[1];
        elseif (preg_match('/^--classes=(\d+)$/', $arg, $m)) $opts['classes'] = (int) $m[1];
        elseif (preg_match('/^--props=(\d+)$/', $arg, $m)) $opts['props'] = (int) $m[1];
    }
    return $opts;
}

// ---------------------------------------------------------------------------
// Main
// ---------------------------------------------------------------------------

$opts = parseArgs($argv);
$iterations = $opts['iterations'];
$classes = min($opts['classes'], 50); // 50 subjects declared above
$props = $opts['props'];

echo "PHP ", PHP_VERSION, "  |  iterations: {$iterations}  |  classes: {$classes}  |  props: {$props}\n";
echo str_repeat('-', 98), "\n";

// ---- 1. Warm-hit throughput ----------------------------------------------
printf("%-28s %-16s %12s %12s %14s %14s\n", 'scenario', 'impl', 'total_ms', 'ns/op', 'ops', 'peak_delta');
echo str_repeat('-', 98), "\n";

$old = measure(fn() => scenarioWarmHits('old', $classes, 2000), $iterations);
$new = measure(fn() => scenarioWarmHits('new', $classes, 2000), $iterations);
printf("%-28s %-16s %12.2f %12.1f %14s %14s\n", 'warm-hit (2000x/class)', 'old (flat)', $old['elapsed_ms'], $old['ns_per_op'], number_format($old['ops']), fmtBytes($old['peak_delta']));
printf("%-28s %-16s %12.2f %12.1f %14s %14s  (%.2fx)\n", '', 'new (nested)', $new['elapsed_ms'], $new['ns_per_op'], number_format($new['ops']), fmtBytes($new['peak_delta']), $old['ns_per_op'] / max(1, $new['ns_per_op']));

// ---- 2. Cold-miss throughput ---------------------------------------------
$old = measure(fn() => scenarioColdMisses('old', $classes, $props), $iterations);
$new = measure(fn() => scenarioColdMisses('new', $classes, $props), $iterations);
printf("%-28s %-16s %12.2f %12.1f %14s %14s\n", "cold-miss ({$props}/class)", 'old (flat)', $old['elapsed_ms'], $old['ns_per_op'], number_format($old['ops']), fmtBytes($old['peak_delta']));
printf("%-28s %-16s %12.2f %12.1f %14s %14s  (%.2fx)\n", '', 'new (nested)', $new['elapsed_ms'], $new['ns_per_op'], number_format($new['ops']), fmtBytes($new['peak_delta']), $old['ns_per_op'] / max(1, $new['ns_per_op']));

echo str_repeat('-', 98), "\n";

// ---- 3. Pathological growth ----------------------------------------------
$growth_props_per_class = 500;
$total_attempted = $classes * $growth_props_per_class;
echo "\nPathological unbounded-growth (dynamic property names, {$total_attempted} total inserts):\n";
echo str_repeat('-', 98), "\n";
printf("%-28s %14s %14s %32s\n", 'impl', 'entries kept', 'peak mem', 'behaviour');
echo str_repeat('-', 98), "\n";

$old = scenarioUnboundedGrowth('old', $classes, $growth_props_per_class);
$new = scenarioUnboundedGrowth('new', $classes, $growth_props_per_class);

printf("%-28s %14s %14s %32s\n", 'old (flat, unbounded)', number_format($old['entries']), fmtBytes($old['peak_bytes']), 'keeps every entry');
printf("%-28s %14s %14s %32s\n", 'new (bounded, limit=10k)', number_format($new['entries']), fmtBytes($new['peak_bytes']), 'self-caps at limit');

echo "\nMemory saved by the cap: ", fmtBytes(max(0, $old['peak_bytes'] - $new['peak_bytes'])),
     sprintf("  (%.1f%% reduction)\n", $old['peak_bytes'] > 0 ? (1 - $new['peak_bytes'] / $old['peak_bytes']) * 100 : 0);
