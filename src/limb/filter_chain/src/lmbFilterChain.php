<?php
/*
 * Limb PHP Framework
 *
 * @license    LGPL http://www.gnu.org/copyleft/lesser.html
 */

namespace limb\filter_chain\src;

use Closure;
use Psr\Http\Message\RequestInterface;

/**
 *  lmbFilterChain is an implementation of the Intercepting Filter design pattern.
 *
 *  A chain holds an ordered list of filters and runs them as a "Russian nested doll":
 *  every filter receives the chain instance and decides whether to continue by calling
 *  $filter_chain->next($request, $callback). When all filters have run, the optional
 *  terminal $callback is invoked with the request.
 *
 *  Because the class itself implements {@see lmbInterceptingFilterInterface}, a whole
 *  chain may be registered as a single filter inside another chain.
 *
 *  Usage:
 *  <code>
 *  $chain = new lmbFilterChain();
 *  $chain->registerFilter(new A());
 *  $chain->registerFilter(new B());
 *  $chain->registerFilter(MyFilter::class, $ctorArg1, $ctorArg2); // lazy instantiation
 *  $response = $chain->process($request, fn($req) => $controller->handle($req));
 *  </code>
 *
 * @package filter_chain
 */
class lmbFilterChain implements lmbInterceptingFilterInterface
{
    /**
     * @var array<int, array{0: object|class-string, 1: array<int, mixed>}>
     */
    protected array $filters = [];

    /**
     * @var int Index of the current active filter while running the chain.
     */
    protected int $counter = -1;

    /**
     * Registers a filter (object or class-string) at the end of the chain.
     * Class-string filters are lazily instantiated with the supplied $args
     * the first time the slot is reached.
     *
     * @param object|class-string $filter
     */
    public function registerFilter($filter, ...$args): static
    {
        $this->filters[] = [$filter, $args];

        return $this;
    }

    /**
     * @return array<int, array{0: object|class-string, 1: array<int, mixed>}>
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * Runs the next filter in the chain, or invokes the terminal callback
     * when no more filters are registered.
     *
     * @return mixed
     */
    public function next(RequestInterface $request, ?Closure $callback = null)
    {
        $this->counter++;

        if (isset($this->filters[$this->counter])) {
            [$filter, $args] = $this->filters[$this->counter];
            if (!is_object($filter)) {
                $filter = new $filter(...$args);
            }

            /** @var lmbInterceptingFilterInterface $filter */
            return $filter->run($this, $request, $callback);
        }

        if ($callback !== null) {
            return $callback($request);
        }

        return null;
    }

    /**
     * Runs the chain from the first filter (resets the internal cursor).
     *
     * @return mixed
     */
    public function process(RequestInterface $request, ?Closure $callback = null)
    {
        $this->counter = -1;

        return $this->next($request, $callback);
    }

    /**
     * Allows a chain to be used as a single filter inside another chain.
     * This chain runs its own filters first; when its pipeline is exhausted,
     * the terminal closure resumes the parent chain via $filter_chain->next().
     *
     * @return mixed
     */
    public function run(lmbInterceptingFilterInterface $filter_chain, RequestInterface $request, ?Closure $callback = null)
    {
        $this->counter = -1;

        return $this->next($request, function (RequestInterface $request) use ($filter_chain, $callback) {
            return $filter_chain->next($request, $callback);
        });
    }
}
