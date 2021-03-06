<?php declare(strict_types=1);

namespace Rector\DeadCode\Tests\Rector\ClassMethod\RemoveUnusedPrivateMethodRector;

use Rector\DeadCode\Rector\ClassMethod\RemoveUnusedPrivateMethodRector;
use Rector\Testing\PHPUnit\AbstractRectorTestCase;

final class RemoveUnusedPrivateMethodRectorTest extends AbstractRectorTestCase
{
    public function test(): void
    {
        $this->doTestFiles([
            __DIR__ . '/Fixture/fixture.php.inc',
            __DIR__ . '/Fixture/static_method.php.inc',
            __DIR__ . '/Fixture/private_constructor.php.inc',
            // skip
            __DIR__ . '/Fixture/keep_anonymous.php.inc',
            __DIR__ . '/Fixture/skip_local_called.php.inc',
            __DIR__ . '/Fixture/keep_in_trait.php.inc',
            __DIR__ . '/Fixture/keep_used_method.php.inc',
            __DIR__ . '/Fixture/keep_used_method_static.php.inc',
            __DIR__ . '/Fixture/skip_anonymous_class.php.inc',
        ]);
    }

    protected function getRectorClass(): string
    {
        return RemoveUnusedPrivateMethodRector::class;
    }
}
