<?php

namespace Millesime\Compilation;

class Steps
{
    /**
     * @var Array[Step]
     */
    private $steps = [];

    /**
     * @param Step $step
     */
    public function add(Step $step)
    {
        $this->steps[] = $step;
    }

    /**
     * @param \Phar $phar
     * @param array $options
     * @return \Phar
     */
    public function apply(\Phar $phar = null, array $options = [])
    {
        return array_reduce(
            $this->steps,
            function ($phar, $step) use ($options) {
                return $step->execute($phar, $options);
            },
            $phar
        );
    }
}
