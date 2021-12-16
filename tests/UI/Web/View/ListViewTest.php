<?php

declare(strict_types=1);

namespace Districts\Test\UI\Web\View;

use Districts\UI\Web\View\ListView;
use Districts\UI\Web\View\OrderingUrlGenerator;
use Districts\UI\Web\View\PageReferenceFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig as TwigView;

/**
 * @covers \Districts\UI\Web\View\ListView
 */
class ListViewTest extends TestCase
{
    private ListView $listView;

    /**
     * @var MockObject|TwigView
     */
    private $twigView;

    private PageReferenceFactory $pageReferenceFactory;

    private OrderingUrlGenerator $orderingUrlGenerator;

    protected function setUp(): void
    {
        $this->twigView = $this->createMock(TwigView::class);
        $this->pageReferenceFactory = $this->createStub(PageReferenceFactory::class);
        $this->orderingUrlGenerator = $this->createStub(OrderingUrlGenerator::class);

        $this->listView = new ListView(
            $this->twigView,
            $this->pageReferenceFactory,
            $this->orderingUrlGenerator
        );
    }

    public function testRenders(): void
    {
        $request = $this->createStub(ServerRequestInterface::class);
        $request
            ->method("getQueryParams")
            ->willReturn([]);
        $this->listView->configure(0, 0, $request, [], []);
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
        $request = $this->createStub(ServerRequestInterface::class);
        $request
            ->method("getQueryParams")
            ->willReturn([]);
        $this->listView->configure(0, 0, $request, [""], []);
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
