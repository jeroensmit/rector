<?php

namespace Rector\Architecture\Tests\Rector\Class_\ConstructorInjectionToActionInjectionRector\MoveMore;

final class SomeController
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function default()
    {
        $this->handleProductStuff();
    }

    public function foo()
    {
        $this->handleProductStuff();
        andSomeThingElse();
    }

    public function bar()
    {
        doesNotUseProductRepository();
    }

    private function handleProductStuff()
    {
        $products = $this->productRepository->fetchAll();
    }


}

?>
    -----
<?php

namespace Rector\Architecture\Tests\Rector\Class_\ConstructorInjectionToActionInjectionRector\MoveMore;

final class SomeController
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function default(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
        $this->handleProductStuff();
    }

    public function foo(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
        $this->handleProductStuff();
        andSomeThingElse();
    }

    public function bar()
    {
        doesNotUseProductRepository();
    }


    private function handleProductStuff()
    {
        $products = $this->productRepository->fetchAll();
    }
}

?>
