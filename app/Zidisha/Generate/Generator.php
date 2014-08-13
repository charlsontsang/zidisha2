<?php

namespace Zidisha\Generate;

use Faker\Factory as Faker;

abstract class Generator
{

    /**
     * @var int
     */
    protected $size = 10;

    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @return static
     */
    public static function create()
    {
        $static = \App::make('\\' . get_called_class());
        $static->faker = Faker::create();

        return $static;
    }

    protected function beforeGenerate()
    {
        // Do some setup work ...
    }

    abstract protected function doGenerate($i);

    public function generate()
    {
        $this->beforeGenerate();

        $return = [];

        for ($i = 1; $i <= $this->size; $i++) {
            $return[] = $this->doGenerate($i);
        }
        
        if ($this->size == 1) {
            return $return[0];
        }

        return $return;
    }

    public function generateOne()
    {
        $this->size = 1;
        return $this->generate();
    }

    /**
     * @param int $size
     * @return $this
     */
    public function size($size)
    {
        $this->size = $size;
        return $this;
    }
}
