<?php

namespace Rector\Php\Tests\Rector\FuncCall\CountOnNullRector\Fixture;

class PropertyWithDoc
{
    /**
     * @var int[]
     */
    private $fail;

    public function run()
    {
        return count($this->fail);
    }
}

?>
-----
<?php

namespace Rector\Php\Tests\Rector\FuncCall\CountOnNullRector\Fixture;

class PropertyWithDoc
{
    /**
     * @var int[]
     */
    private $fail;

    public function run()
    {
        return is_array($this->fail) || $this->fail instanceof \Countable ? count($this->fail) : 0;
    }
}

?>
