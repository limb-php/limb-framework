<?php
/**
 * Microbenchmark: lmbToolkit — old (extends lmbObject) vs. new (standalone bag).
 *
 * Both implementations are inlined below so this bench stays self-contained:
 * we don't need to toggle the real framework or check out previous commits.
 *
 * Scenarios measured (each run `iterations` times):
 *   1. Construction cost.
 *   2. setRaw / getRaw round-trip (tool internals).
 *   3. set / get round-trip for a raw variable (no tool match).
 *   4. has() hit + has() miss.
 *   5. set / get routed through a tool's setFoo/getFoo (tool-dispatch path).
 *   6. __call dispatch to a tool signature.
 *
 * A separate memory-footprint section reports average bytes per instance.
 *
 * Usage:
 *   C:\php-8.2\php.exe benchmarks/toolkitInheritanceBench.php
 *   C:\php-8.2\php.exe benchmarks/toolkitInheritanceBench.php --iterations=7 --ops=100000
 */

declare(strict_types=1);

namespace bench_toolkit;

require_once __DIR__ . '/../vendor/autoload.php';

use limb\config\src\toolkit\lmbConfTools;
use limb\fs\src\toolkit\lmbFsTools;
use limb\cache\src\toolkit\lmbCacheTools;
use limb\net\src\toolkit\lmbNetTools;
use limb\log\src\toolkit\lmbLogTools;

// ---------------------------------------------------------------------------
// OLD implementation: minimal faithful copy of lmbObject + lmbToolkit-extends-it.
// Only the paths exercised by the scenarios below are reproduced.
// ---------------------------------------------------------------------------

class OldLmbObject
{
    protected $__properties = [];
    public static $map_p2m = [];
    private static int $map_p2m_count = 0;
    private static int $map_p2m_limit = 10000;

    private $_map = [
        'public' => [],
        'dynamic' => [],
        'initialized' => false,
    ];

    function __construct($properties = [])
    {
        $this->_registerPredefinedVariables();
        if ($properties) $this->import($properties);
    }

    protected function _registerPredefinedVariables()
    {
        if ($this->_map['initialized']) return;
        $var_names = get_object_vars($this);
        $var_names = array_merge_recursive($var_names, $this->__properties);
        foreach ($var_names as $key => $_) {
            if (!$this->_isGuarded($key))
                $this->_map['public'][$key] = $key;
        }
        $this->_map['initialized'] = true;
    }

    function import($values)
    {
        if (!is_array($values)) return;
        foreach ($values as $property => $value)
            $this->_setRaw($property, $value);
    }

    function export()
    {
        $exported = [];
        foreach ($this->getPropertiesNames() as $name) {
            if (property_exists($this, $name)) $exported[$name] = $this->$name;
            else $exported[$name] = $this->__properties[$name];
        }
        return $exported;
    }

    function has($name): bool
    {
        return $this->_hasProperty($name) || $this->_mapPropertyToMethod($name);
    }

    function getPropertiesNames(): array
    {
        $this->_registerPredefinedVariables();
        return array_keys($this->_map['public']);
    }

    protected function _hasProperty($name): bool
    {
        $this->_registerPredefinedVariables();
        return array_key_exists($name, $this->_map['public']);
    }

    function get($name, $default = null)
    {
        if ($method = $this->_mapPropertyToMethod($name))
            return $this->$method();
        if ($this->_hasProperty($name))
            return $this->_getRaw($name);
        if ($default !== null) return $default;
        throw new \RuntimeException("No such property '$name'");
    }

    function set($name, $value)
    {
        $method = 'set' . ucfirst($name);
        if (method_exists($this, $method) && $method !== 'set') {
            $this->$method($value);
            return $this;
        }
        $this->_setRaw($name, $value);
        return $this;
    }

    protected function _getRaw($name)
    {
        if ($this->_hasProperty($name)) {
            if (property_exists($this, $name)) return $this->$name ?? null;
            return $this->__properties[$name] ?? null;
        }
        return null;
    }

    protected function _setRaw($name, $value)
    {
        if ($this->_isGuarded($name)) return;
        $this->_map['public'][$name] = $name;
        $this->_map['dynamic'][$name] = $name;
        if (property_exists($this, $name)) $this->$name = $value;
        else $this->__properties[$name] = $value;
    }

    protected function _isGuarded($property): bool
    {
        return isset($property[0]) && $property[0] === '_';
    }

    protected function _mapPropertyToMethod($property)
    {
        $class = static::class;
        if (isset(self::$map_p2m[$class]) && array_key_exists($property, self::$map_p2m[$class]))
            return self::$map_p2m[$class][$property];

        $method = 'get' . ucfirst($property);
        $resolved = method_exists($this, $method) ? $method : false;

        if (self::$map_p2m_limit > 0 && self::$map_p2m_count >= self::$map_p2m_limit) {
            self::$map_p2m = [];
            self::$map_p2m_count = 0;
        }
        if (!isset(self::$map_p2m[$class][$property])) self::$map_p2m_count++;
        self::$map_p2m[$class][$property] = $resolved;
        return $resolved;
    }

    public static function clearP2MCache(): void
    {
        self::$map_p2m = [];
        self::$map_p2m_count = 0;
    }
}

class OldLmbToolkit extends OldLmbObject
{
    protected $_tools = [];
    protected $_tools_signatures = [];
    protected $_signatures_loaded = false;
    protected $_id;

    function __construct()
    {
        parent::__construct();
        $this->_id = uniqid();
    }

    function addTool(object $tool, string $name = ''): void
    {
        if (!$name) $name = (new \ReflectionClass($tool))->getShortName();
        $this->_tools[$name] = $tool;
        $this->_tools_signatures = [];
        $this->_signatures_loaded = false;
    }

    function hasTool(string $name): bool
    {
        return isset($this->_tools[$name]);
    }

    function set($name, $value)
    {
        if ($method = $this->_mapPropertyToSetMethod($name))
            $this->$method($value);
        else
            parent::set($name, $value);
    }

    function get($name, $default = null)
    {
        if ($method = $this->_mapPropertyToGetMethod($name))
            return $this->$method();
        return parent::get($name, $default);
    }

    function has($name): bool
    {
        return $this->_hasGetMethodFor($name) || parent::has($name);
    }

    function setRaw($var, $value) { parent::_setRaw($var, $value); }
    function getRaw($var)         { return parent::_getRaw($var); }

    public function __call($method, $args = [])
    {
        $this->_ensureSignatures();
        if (isset($this->_tools_signatures[$method]))
            return call_user_func_array([$this->_tools_signatures[$method], $method], $args);
        throw new \BadMethodCallException("No method '$method'");
    }

    protected function _ensureSignatures(): void
    {
        if ($this->_signatures_loaded) return;
        $this->_tools_signatures = [];
        foreach ($this->_tools as $tool) {
            foreach ($tool->getToolsSignatures() as $m => $obj) {
                if (!isset($this->_tools_signatures[$m]))
                    $this->_tools_signatures[$m] = $obj;
            }
        }
        $this->_signatures_loaded = true;
    }

    protected function _hasGetMethodFor($p): bool
    {
        return (bool) $this->_mapPropertyToGetMethod($p);
    }

    protected function _mapPropertyToGetMethod($p)
    {
        $this->_ensureSignatures();
        $m = 'get' . ucfirst($p);
        return isset($this->_tools_signatures[$m]) ? $m : false;
    }

    protected function _mapPropertyToSetMethod($p)
    {
        $this->_ensureSignatures();
        $m = 'set' . ucfirst($p);
        return isset($this->_tools_signatures[$m]) ? $m : false;
    }
}

// ---------------------------------------------------------------------------
// NEW implementation: faithful copy of the refactored lmbToolkit (no lmbObject).
// ---------------------------------------------------------------------------

class NewLmbToolkit
{
    protected $_tools = [];
    protected $_tools_signatures = [];
    protected $_signatures_loaded = false;
    protected $_id;
    private array $_vars = [];

    function __construct()
    {
        $this->_id = uniqid();
    }

    function addTool(object $tool, string $name = ''): void
    {
        if (!$name) $name = (new \ReflectionClass($tool))->getShortName();
        $this->_tools[$name] = $tool;
        $this->_tools_signatures = [];
        $this->_signatures_loaded = false;
    }

    function hasTool(string $name): bool
    {
        return isset($this->_tools[$name]);
    }

    function set($name, $value)
    {
        if ($method = $this->_mapPropertyToSetMethod($name))
            $this->$method($value);
        else
            $this->setRaw($name, $value);
    }

    function get($name, $default = null)
    {
        if ($method = $this->_mapPropertyToGetMethod($name))
            return $this->$method();
        if (array_key_exists($name, $this->_vars))
            return $this->_vars[$name];
        if ($default !== null) return $default;
        throw new \RuntimeException("No such property '$name'");
    }

    function has($name): bool
    {
        return $this->_hasGetMethodFor($name) || array_key_exists($name, $this->_vars);
    }

    function setRaw($var, $value)
    {
        if (isset($var[0]) && $var[0] === '_') return;
        $this->_vars[$var] = $value;
    }

    function getRaw($var) { return $this->_vars[$var] ?? null; }

    function export(): array { return $this->_vars; }

    function import($values): void
    {
        if (!is_array($values)) return;
        foreach ($values as $k => $v) $this->setRaw($k, $v);
    }

    function reset(): void { $this->_vars = []; }

    public function __call($method, $args = [])
    {
        $this->_ensureSignatures();
        if (isset($this->_tools_signatures[$method]))
            return call_user_func_array([$this->_tools_signatures[$method], $method], $args);
        throw new \BadMethodCallException("No method '$method'");
    }

    protected function _ensureSignatures(): void
    {
        if ($this->_signatures_loaded) return;
        $this->_tools_signatures = [];
        foreach ($this->_tools as $tool) {
            foreach ($tool->getToolsSignatures() as $m => $obj) {
                if (!isset($this->_tools_signatures[$m]))
                    $this->_tools_signatures[$m] = $obj;
            }
        }
        $this->_signatures_loaded = true;
    }

    protected function _hasGetMethodFor($p): bool
    {
        return (bool) $this->_mapPropertyToGetMethod($p);
    }

    protected function _mapPropertyToGetMethod($p)
    {
        $this->_ensureSignatures();
        $m = 'get' . ucfirst($p);
        return isset($this->_tools_signatures[$m]) ? $m : false;
    }

    protected function _mapPropertyToSetMethod($p)
    {
        $this->_ensureSignatures();
        $m = 'set' . ucfirst($p);
        return isset($this->_tools_signatures[$m]) ? $m : false;
    }
}

// ---------------------------------------------------------------------------
// Shared tool fixtures
// ---------------------------------------------------------------------------

interface OldToolInterface { public function getToolsSignatures(): array; }
interface NewToolInterface { public function getToolsSignatures(): array; }

class OldSampleTool implements OldToolInterface
{
    public int $calls = 0;
    public function getVar()           { return $this->_var ?? null; }
    public function setVar($value)     { $this->_var = $value; }
    public function commonMethod()     { $this->calls++; return 'ok'; }
    public function getToolsSignatures(): array
    {
        return ['getVar' => $this, 'setVar' => $this, 'commonMethod' => $this];
    }
    private $_var;
}

class NewSampleTool implements NewToolInterface
{
    public int $calls = 0;
    public function getVar()           { return $this->_var ?? null; }
    public function setVar($value)     { $this->_var = $value; }
    public function commonMethod()     { $this->calls++; return 'ok'; }
    public function getToolsSignatures(): array
    {
        return ['getVar' => $this, 'setVar' => $this, 'commonMethod' => $this];
    }
    private $_var;
}

// ---------------------------------------------------------------------------
// Scenarios
// ---------------------------------------------------------------------------

function scenarioConstruct(string $impl, int $ops): int
{
    if ($impl === 'old') {
        OldLmbObject::clearP2MCache();
        for ($i = 0; $i < $ops; $i++) $t = new OldLmbToolkit();
    } else {
        for ($i = 0; $i < $ops; $i++) $t = new NewLmbToolkit();
    }
    return $ops;
}

function scenarioRawSetGet(string $impl, int $ops): int
{
    $t = $impl === 'old' ? new OldLmbToolkit() : new NewLmbToolkit();
    for ($i = 0; $i < $ops; $i++) {
        $key = 'k' . ($i % 64);
        $t->setRaw($key, $i);
        $_ = $t->getRaw($key);
    }
    return $ops * 2;
}

function scenarioSetGetNoTool(string $impl, int $ops): int
{
    // No tools registered: set()/get() fall straight through to the bag.
    $t = $impl === 'old' ? new OldLmbToolkit() : new NewLmbToolkit();
    for ($i = 0; $i < $ops; $i++) {
        $key = 'k' . ($i % 64);
        $t->set($key, $i);
        $_ = $t->get($key);
    }
    return $ops * 2;
}

function scenarioHasHitMiss(string $impl, int $ops): int
{
    $t = $impl === 'old' ? new OldLmbToolkit() : new NewLmbToolkit();
    // Pre-populate 16 keys to create hit targets.
    for ($i = 0; $i < 16; $i++) $t->setRaw('k' . $i, $i);

    for ($i = 0; $i < $ops; $i++) {
        $_ = $t->has('k' . ($i % 16));  // hit
        $_ = $t->has('missing_' . ($i % 100));  // miss
    }
    return $ops * 2;
}

function scenarioSetGetViaToolGetter(string $impl, int $ops): int
{
    if ($impl === 'old') {
        $t = new OldLmbToolkit();
        $t->addTool(new OldSampleTool());
    } else {
        $t = new NewLmbToolkit();
        $t->addTool(new NewSampleTool());
    }
    // 'var' routes through the tool's setVar/getVar — the most expensive
    // set/get shape: each call walks the signatures table.
    for ($i = 0; $i < $ops; $i++) {
        $t->set('var', $i);
        $_ = $t->get('var');
    }
    return $ops * 2;
}

function scenarioCallDispatch(string $impl, int $ops): int
{
    if ($impl === 'old') {
        $t = new OldLmbToolkit();
        $t->addTool(new OldSampleTool());
    } else {
        $t = new NewLmbToolkit();
        $t->addTool(new NewSampleTool());
    }
    for ($i = 0; $i < $ops; $i++) {
        $_ = $t->commonMethod();  // __call -> signatures lookup -> tool
    }
    return $ops;
}

function scenarioMemoryPerInstance(string $impl, int $instances): array
{
    gc_collect_cycles();
    $mem0 = memory_get_usage();

    $holder = [];
    for ($i = 0; $i < $instances; $i++) {
        $holder[] = $impl === 'old' ? new OldLmbToolkit() : new NewLmbToolkit();
    }

    $mem1 = memory_get_usage();
    $bytes_per_instance = ($mem1 - $mem0) / $instances;

    // Prevent the optimiser from dropping the array before we're done.
    $count = count($holder);
    unset($holder);
    gc_collect_cycles();

    return ['bytes_per_instance' => $bytes_per_instance, 'count' => $count];
}

// ---------------------------------------------------------------------------
// Harness
// ---------------------------------------------------------------------------

function measure(callable $fn, int $iterations): array
{
    gc_collect_cycles();
    memory_reset_peak_usage();
    $mem0 = memory_get_usage();

    $t0 = hrtime(true);
    $total_ops = 0;
    for ($i = 0; $i < $iterations; $i++) $total_ops += (int) $fn();
    $elapsed_ns = hrtime(true) - $t0;

    return [
        'elapsed_ms' => $elapsed_ns / 1_000_000,
        'ops'        => $total_ops,
        'ns_per_op'  => $total_ops > 0 ? $elapsed_ns / $total_ops : 0.0,
        'peak_delta' => max(0, memory_get_peak_usage() - $mem0),
    ];
}

function fmtBytes(int|float $b): string
{
    $b = (int) $b;
    if ($b < 1024) return $b . ' B';
    if ($b < 1024 * 1024) return sprintf('%.1f KiB', $b / 1024);
    return sprintf('%.2f MiB', $b / 1024 / 1024);
}

function parseArgs(array $argv): array
{
    $opts = ['iterations' => 5, 'ops' => 50_000];
    foreach (array_slice($argv, 1) as $arg) {
        if (preg_match('/^--iterations=(\d+)$/', $arg, $m)) $opts['iterations'] = (int) $m[1];
        elseif (preg_match('/^--ops=(\d+)$/', $arg, $m))    $opts['ops']        = (int) $m[1];
    }
    return $opts;
}

function printRow(string $label, string $impl, array $r, ?float $baseline = null): void
{
    $delta = $baseline !== null && $r['ns_per_op'] > 0
        ? sprintf('  (%.2fx)', $baseline / $r['ns_per_op'])
        : '';
    printf(
        "%-36s %-14s %10.2f %12.1f %16s %14s%s\n",
        $label,
        $impl,
        $r['elapsed_ms'],
        $r['ns_per_op'],
        number_format($r['ops']),
        fmtBytes($r['peak_delta']),
        $delta
    );
}

// ---------------------------------------------------------------------------
// Main
// ---------------------------------------------------------------------------

$opts = parseArgs($argv);
$iterations = $opts['iterations'];
$ops = $opts['ops'];

echo "PHP ", PHP_VERSION, "  |  iterations: {$iterations}  |  ops/iter: ", number_format($ops), "\n";
echo str_repeat('=', 110), "\n";
echo "lmbToolkit — OLD (extends lmbObject)  vs.  NEW (standalone)\n";
echo str_repeat('=', 110), "\n";

printf(
    "%-36s %-14s %10s %12s %16s %14s\n",
    'scenario', 'impl', 'total_ms', 'ns/op', 'ops', 'peak_delta'
);
echo str_repeat('-', 110), "\n";

$scenarios = [
    'construct'                           => fn(string $impl) => scenarioConstruct($impl, $ops),
    'setRaw/getRaw round-trip'            => fn(string $impl) => scenarioRawSetGet($impl, $ops),
    'set/get (no tool, raw bag)'          => fn(string $impl) => scenarioSetGetNoTool($impl, $ops),
    'has() hit + miss'                    => fn(string $impl) => scenarioHasHitMiss($impl, $ops),
    'set/get via tool setter/getter'      => fn(string $impl) => scenarioSetGetViaToolGetter($impl, $ops),
    '__call -> tool dispatch'             => fn(string $impl) => scenarioCallDispatch($impl, $ops),
];

foreach ($scenarios as $label => $fn) {
    $old = measure(fn() => $fn('old'), $iterations);
    $new = measure(fn() => $fn('new'), $iterations);
    printRow($label, 'old', $old);
    printRow('', 'new', $new, $old['ns_per_op']);
    echo str_repeat('-', 110), "\n";
}

// -----------------------------------------------------------------------
// Memory footprint per instance
// -----------------------------------------------------------------------
echo "\nMemory footprint per instance (10,000 live toolkits held in an array):\n";
echo str_repeat('-', 110), "\n";
printf("%-20s %20s %18s\n", 'impl', 'bytes/instance', 'delta vs old');
echo str_repeat('-', 110), "\n";

$old_mem = scenarioMemoryPerInstance('old', 10_000);
$new_mem = scenarioMemoryPerInstance('new', 10_000);

printf("%-20s %20.1f %18s\n", 'old (extends lmbObject)', $old_mem['bytes_per_instance'], '—');
$saved = $old_mem['bytes_per_instance'] - $new_mem['bytes_per_instance'];
$pct   = $old_mem['bytes_per_instance'] > 0
    ? ($saved / $old_mem['bytes_per_instance']) * 100
    : 0.0;
printf(
    "%-20s %20.1f %18s  (%.1f%% %s)\n",
    'new (standalone)',
    $new_mem['bytes_per_instance'],
    ($saved >= 0 ? '-' : '+') . fmtBytes(abs($saved)),
    abs($pct),
    $saved >= 0 ? 'reduction' : 'increase'
);

// ---------------------------------------------------------------------------
// REAL-TOOLS SECTION
//
// Same toolkit implementations, but now populated with a realistic mix of
// framework tools: lmbConfTools, lmbFsTools, lmbCacheTools, lmbNetTools,
// lmbLogTools. These extend lmbAbstractTools and therefore build a realistic
// signatures table (dozens of entries) via reflection.
//
// We dispatch to methods that have no external dependencies, so results are
// purely toolkit overhead, not tool-body work:
//   - lmbConfTools::setConfIncludePath / getConfIncludePath (local property)
//   - lmbConfTools::commonMethod is N/A — we just use setConfIncludePath
// ---------------------------------------------------------------------------

echo "\n";
echo str_repeat('=', 110), "\n";
echo "REAL framework tools registered (lmbConfTools, lmbFsTools, lmbCacheTools, lmbNetTools, lmbLogTools)\n";
echo str_repeat('=', 110), "\n";

/**
 * Build a toolkit loaded with real framework tools.
 * Returns [toolkit, total_signatures].
 */
function buildLoaded(string $impl): array
{
    $t = $impl === 'old' ? new OldLmbToolkit() : new NewLmbToolkit();

    // Construct the real tools. Each one's constructor wires up
    // $this->toolkit = lmbToolkit::instance() — that's the real (new)
    // singleton in the working tree; harmless for our purposes because the
    // methods we dispatch never call $this->toolkit->...
    $tools = [
        new lmbConfTools(),
        new lmbFsTools(),
        new lmbCacheTools(),
        new lmbNetTools(),
        new lmbLogTools(),
    ];
    foreach ($tools as $tool) $t->addTool($tool);

    // Force the signatures table to be built so we can measure its size.
    // Dispatching any registered method triggers _ensureSignatures().
    $t->getConfIncludePath();
    $ref = new \ReflectionClass($t);
    $prop = $ref->getProperty('_tools_signatures');
    $prop->setAccessible(true);
    $sigs = count($prop->getValue($t));

    return [$t, $sigs];
}

// Advertise the signature table size so readers know what they're paying for.
[$tmp, $old_sigs] = buildLoaded('old');
[$tmp, $new_sigs] = buildLoaded('new');
printf("Signatures registered: old=%d, new=%d  (same tools, identical surface)\n\n", $old_sigs, $new_sigs);
unset($tmp);

printf(
    "%-40s %-14s %10s %12s %16s %14s\n",
    'scenario (real tools)', 'impl', 'total_ms', 'ns/op', 'ops', 'peak_delta'
);
echo str_repeat('-', 110), "\n";

// --- A. First-time _ensureSignatures() flush -------------------------------
//
// Every fresh toolkit pays this cost once to build its signatures table.
// We measure the FULL "add 5 tools + trigger first dispatch" flow.
function scenarioFirstFlush(string $impl, int $ops): int
{
    for ($i = 0; $i < $ops; $i++) {
        [$t, $_] = buildLoaded($impl);  // addTool x5 + first dispatch
        unset($t);
    }
    return $ops;
}
$first_flush_ops = max(200, (int)($ops / 200));  // expensive; do fewer
$old = measure(fn() => scenarioFirstFlush('old', $first_flush_ops), $iterations);
$new = measure(fn() => scenarioFirstFlush('new', $first_flush_ops), $iterations);
printRow('first _ensureSignatures flush', 'old', $old);
printRow('', 'new', $new, $old['ns_per_op']);
echo str_repeat('-', 110), "\n";

// --- B. __call dispatch to a real tool method -----------------------------
function scenarioRealCallDispatch(string $impl, int $ops): int
{
    [$t, $_] = buildLoaded($impl);
    for ($i = 0; $i < $ops; $i++) {
        $_ = $t->getConfIncludePath();
    }
    return $ops;
}
$old = measure(fn() => scenarioRealCallDispatch('old', $ops), $iterations);
$new = measure(fn() => scenarioRealCallDispatch('new', $ops), $iterations);
printRow('__call -> getConfIncludePath', 'old', $old);
printRow('', 'new', $new, $old['ns_per_op']);
echo str_repeat('-', 110), "\n";

// --- C. set/get via real tool setter/getter -------------------------------
function scenarioRealSetGetViaTool(string $impl, int $ops): int
{
    [$t, $_] = buildLoaded($impl);
    for ($i = 0; $i < $ops; $i++) {
        $t->set('confIncludePath', '/path/' . $i);
        $_ = $t->get('confIncludePath');
    }
    return $ops * 2;
}
$old = measure(fn() => scenarioRealSetGetViaTool('old', $ops), $iterations);
$new = measure(fn() => scenarioRealSetGetViaTool('new', $ops), $iterations);
printRow('set/get via confIncludePath', 'old', $old);
printRow('', 'new', $new, $old['ns_per_op']);
echo str_repeat('-', 110), "\n";

// --- D. has() on a tool-provided virtual property --------------------------
function scenarioRealHas(string $impl, int $ops): int
{
    [$t, $_] = buildLoaded($impl);
    for ($i = 0; $i < $ops; $i++) {
        $_ = $t->has('confIncludePath'); // hit — tool-provided getter exists
        $_ = $t->has('absolutely_not_here'); // miss — neither raw nor tool
    }
    return $ops * 2;
}
$old = measure(fn() => scenarioRealHas('old', $ops), $iterations);
$new = measure(fn() => scenarioRealHas('new', $ops), $iterations);
printRow('has() hit (tool) + miss', 'old', $old);
printRow('', 'new', $new, $old['ns_per_op']);
echo str_repeat('-', 110), "\n";

// --- E. Memory footprint WITH real tools registered ------------------------
echo "\nMemory footprint of a fully-loaded toolkit (5 real tools, signatures built):\n";
echo str_repeat('-', 110), "\n";
printf("%-28s %22s %20s\n", 'impl', 'bytes/loaded instance', 'delta vs old');
echo str_repeat('-', 110), "\n";

function scenarioLoadedMemory(string $impl, int $instances): float
{
    gc_collect_cycles();
    $mem0 = memory_get_usage();

    $holder = [];
    for ($i = 0; $i < $instances; $i++) {
        [$t, $_] = buildLoaded($impl);
        $holder[] = $t;
    }
    $mem1 = memory_get_usage();

    $bytes_per = ($mem1 - $mem0) / $instances;
    unset($holder);
    gc_collect_cycles();
    return $bytes_per;
}

$old_loaded = scenarioLoadedMemory('old', 500);
$new_loaded = scenarioLoadedMemory('new', 500);
printf("%-28s %22.1f %20s\n", 'old (extends lmbObject)', $old_loaded, '—');
$saved = $old_loaded - $new_loaded;
$pct   = $old_loaded > 0 ? ($saved / $old_loaded) * 100 : 0.0;
printf(
    "%-28s %22.1f %20s  (%.1f%% %s)\n",
    'new (standalone)',
    $new_loaded,
    ($saved >= 0 ? '-' : '+') . fmtBytes((int)abs($saved)),
    abs($pct),
    $saved >= 0 ? 'reduction' : 'increase'
);

echo "\n";
