<?php

namespace Core\Contracts;

interface Container
{
    /**
     * Determine if the given abstract type has been bound.
     *
     * @param string $abstract
     *
     * @return bool
     */
    public function bound($abstract);

    /**
     * Register a binding with the container.
     *
     * @param string|array         $abstract
     * @param \Closure|string|null $concrete
     * @param bool                 $shared
     */
    public function bind($abstract, $concrete = null, $shared = false);

    /**
     * Register a binding if it hasn't already been registered.
     *
     * @param string               $abstract
     * @param \Closure|string|null $concrete
     * @param bool                 $shared
     */
    public function bindIf($abstract, $concrete = null, $shared = false);

    /**
     * Register a shared binding in the container.
     *
     * @param string|array         $abstract
     * @param \Closure|string|null $concrete
     */
    public function singleton($abstract, $concrete = null);

    /**
     * Register an existing instance as shared in the container.
     *
     * @param string $abstract
     * @param mixed  $instance
     */
    public function instance($abstract, $instance);

    /**
     * Resolve the given type from the container.
     *
     * @param string $abstract
     *
     * @return mixed
     */
    public function make($abstract);
}
