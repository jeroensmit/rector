<?php declare(strict_types=1);

namespace Rector\CodeQuality\Tests\Rector\FuncCall\InArrayAndArrayKeysToArrayKeyExistsRector;

use Rector\CodeQuality\Rector\FuncCall\InArrayAndArrayKeysToArrayKeyExistsRector;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class InArrayAndArrayKeysToArrayKeyExistsRectorTest extends AbstractRectorTestCase
{
    public function test(): void
    {
        $this->doTestFiles([__DIR__ . '/Fixture/fixture.php.inc']);
    }

    public function getRectorClass(): string
    {
        return InArrayAndArrayKeysToArrayKeyExistsRector::class;
    }
}
