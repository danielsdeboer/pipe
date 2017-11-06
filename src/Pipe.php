<?php

namespace Aviator\Pipe;

class Pipe
{
    /**
     * A placeholder for the value when piping.
     * @const string
     */
    protected $placeholder;

    /**
     * The value being mutated.
     * @var mixed
     */
    protected $value;

    /**
     * Constructor.
     * @param mixed $value
     * @param string $placeholder
     */
    public function __construct($value, string $placeholder = '$$')
    {
        $this->value = $value;
        $this->placeholder = $placeholder;
    }

    /**
     * Static constructor.
     * @param $value
     * @param string $placeholder
     * @return \Aviator\Pipe\Pipe
     */
    public static function take ($value, string $placeholder = '$$')
    {
        return new self($value, $placeholder);
    }

    /**
     * @param callable $callable
     * @param array ...$args
     * @return \Aviator\Pipe\Pipe $this
     */
    public function pipe (callable $callable, ...$args) : self
    {
        $this->value = $callable(
            ...$this->prepareArgs($args)
        );

        return $this;
    }

    /**
     * Get the value.
     * @return mixed
     */
    public function get ()
    {
        return $this->value;
    }

    /**
     * Prepare the arguments list.
     * @param array $args
     * @return array
     */
    protected function prepareArgs (array $args) : array
    {
        return $this->hasPlaceholder($args)
            ? $this->replacePlaceholderWithValue($args)
            : $this->addValueAsFirstArg($args);
    }

    /**
     * Check if an array contains an element matching the placeholder.
     * @param array $args
     * @return bool
     */
    protected function hasPlaceholder (array $args) : bool
    {
        return in_array($this->placeholder, $args, true);
    }

    /**
     * Add the value as the first argument in the arguments list.
     * @param array $args
     * @return array
     */
    protected function addValueAsFirstArg (array $args) : array
    {
        array_unshift($args, $this->value);

        return $args;
    }

    /**
     * Replace any occurrence of the ID constant with the value.
     * @param array $args
     * @return array
     */
    protected function replacePlaceholderWithValue (array $args) : array
    {
        return array_map(function ($arg) {
            return $arg === $this->placeholder
                ? $this->value
                : $arg;
        }, $args);
    }

    /**
     * Call methods that don't exist against the pipe method.
     * @param $name
     * @param $args
     * @return \Aviator\Pipe\Pipe
     */
    public function __call ($name, $args)
    {
        return $this->pipe($name, ...$args);
    }
}
