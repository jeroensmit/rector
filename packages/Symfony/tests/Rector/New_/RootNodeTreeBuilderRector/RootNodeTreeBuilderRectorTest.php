<?php declare(strict_types=1);

namespace Rector\Symfony\Tests\Rector\New_\RootNodeTreeBuilderRector;

use Rector\Symfony\Rector\New_\RootNodeTreeBuilderRector;
use Rector\Symfony\Tests\Rector\New_\RootNodeTreeBuilderRector\Source\TreeBuilder;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class RootNodeTreeBuilderRectorTest extends AbstractRectorTestCase
{
    public function test(): void
    {
        $this->doTestFiles([__DIR__ . '/Fixture/fixture.php.inc', __DIR__ . '/Fixture/fixture2.php.inc']);
    }

    /**
     * @return mixed[]
     */
    protected function getRectorsWithConfiguration(): array
    {
        return [
            RootNodeTreeBuilderRector::class => [
                '$treeBuilderClass' => TreeBuilder::class,
            ],
        ];
    }
}
