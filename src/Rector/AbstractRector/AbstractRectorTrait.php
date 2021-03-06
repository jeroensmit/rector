<?php declare(strict_types=1);

namespace Rector\Rector\AbstractRector;

trait AbstractRectorTrait
{
    use AppliedRectorCollectorTrait;
    use NodeTypeResolverTrait;
    use NameResolverTrait;
    use ConstFetchAnalyzerTrait;
    use BetterStandardPrinterTrait;
    use NodeCommandersTrait;
    use NodeFactoryTrait;
    use VisibilityTrait;
    use ValueResolverTrait;
    use CallableNodeTraverserTrait;
}
