<?php

namespace Weble\JoomlaTestBench\Input;

use Joomla\CMS\Input\Input;
use Joomla\Filter\InputFilter;

class TestInput extends Input
{
    protected static $sourceKeys = [
        'REQUEST',
        'GET',
        'POST',
        'FILES',
        'SERVER',
        'ENV',
        'COOKIES',
    ];

    protected $sources;

    public function __construct(array $sources, array $options = [])
    {
        $this->sources         = array_fill_keys(self::$sourceKeys, []);
        $this->sources['BODY'] = null;

        $this->sources = array_merge($this->sources, $sources);

        $this->filter  = $options['filter'] ?? new InputFilter();
        $this->options = $options;
    }

    /**
     * Magic method to get an input object
     *
     * @param mixed $name Name of the input object to retrieve.
     *
     * @return  Input  The request input object
     *
     * @since   1.0
     */
    public function __get($name)
    {
        if (isset($this->inputs[$name])) {
            return $this->inputs[$name];
        }

        $className = '\\Joomla\\Input\\' . ucfirst($name);
        $source    = $this->sources[strtoupper($name)] ?? null;

        if (class_exists($className)) {
            if ($name === 'json' && $this->sources['BODY']) {
                $source = $this->sources['BODY'];
            }
            $this->inputs[$name] = new $className($source, $this->options);

            return $this->inputs[$name];
        }

        $this->inputs[$name] = new Input($source, $this->options);

        return $this->inputs[$name];
    }

    public function getData(): array
    {
        $data = [];
        foreach ($this->sources as $source) {
            if (is_array($source)) {
                $data = array_merge($data, $source);
            }
        }

        return $data;
    }
}
