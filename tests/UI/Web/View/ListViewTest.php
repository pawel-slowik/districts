<?php

declare(strict_types=1);

namespace Districts\Test\UI\Web;

use Districts\UI\Web\View\ListView;
use Districts\UI\Web\View\OrderingUrlGenerator;
use Districts\UI\Web\View\RoutedPageReferenceFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig as TwigView;

/**
 * @covers \Districts\UI\Web\View\ListView
 */
class ListViewTest extends TestCase
{
    /**
     * @var ListView
     */
    private $listView;

    /**
     * @var MockObject|TwigView
     */
    private $twigView;

    /**
     * @var RoutedPageReferenceFactory|Stub
     */
    private $routedPageReferenceFactory;

    /**
     * @var OrderingUrlGenerator|Stub
     */
    private $orderingUrlGenerator;

    protected function setUp(): void
    {
        $this->twigView = $this->createMock(TwigView::class);
        $this->routedPageReferenceFactory = $this->createStub(RoutedPageReferenceFactory::class);
        $this->orderingUrlGenerator = $this->createStub(OrderingUrlGenerator::class);

        $this->listView = new ListView(
            $this->twigView,
            $this->routedPageReferenceFactory,
            $this->orderingUrlGenerator
        );
    }

    public function testRenders(): void
    {
        $this->listView->configurePagination(0, 0, $this->createStub(ServerRequestInterface::class));
        $this->listView->configureOrdering("", [], [], []);
        $this->twigView
            ->expects($this->once())
            ->method("render");

        $this->listView->render($this->createStub(ResponseInterface::class), "", []);
    }

    /**
     * @dataProvider computedDataKeyProvider
     */
    public function testSetsComputedDataKey(string $key): void
    {
        $this->listView->configurePagination(0, 0, $this->createStub(ServerRequestInterface::class));
        $this->listView->configureOrdering("", [""], [], []);
        $this->twigView
            ->expects($this->once())
            ->method("render")
            ->with(
                $this->anything(),
                $this->anything(),
                $this->callback(
                    function (array $data) use ($key): bool {
                        return array_key_exists($key, $data);
                    }
                )
            );

        $this->listView->render($this->createStub(ResponseInterface::class), "", []);
    }

    public function computedDataKeyProvider(): array
    {
        return [
            ["orderingUrls"],
            ["pagination"],
        ];
    }
}
