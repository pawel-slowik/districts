<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\UI\View;

use Districts\Editor\Domain\District;
use Districts\Editor\Domain\PaginatedResult;
use Districts\Editor\UI\View\ListView;
use Districts\Editor\UI\View\OrderingUrlGenerator;
use Districts\Editor\UI\View\PageReferenceFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig as TwigView;

/**
 * @covers \Districts\Editor\UI\View\ListView
 */
class ListViewTest extends TestCase
{
    /** @var ListView<District> */
    private ListView $listView;

    /** @var MockObject&TwigView */
    private TwigView $twigView;

    /** @var PageReferenceFactory&Stub */
    private PageReferenceFactory $pageReferenceFactory;

    /** @var OrderingUrlGenerator&Stub */
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
        $paginatedResult = $this->createStub(PaginatedResult::class);
        $request = $this->createStub(ServerRequestInterface::class);
        $request
            ->method("getQueryParams")
            ->willReturn([]);
        $this->twigView
            ->expects($this->once())
            ->method("render");

        $this->listView->render($this->createStub(ResponseInterface::class), $paginatedResult, $request, [], "", []);
    }

    /**
     * @dataProvider computedDataKeyProvider
     */
    public function testSetsComputedDataKey(string $key): void
    {
        $paginatedResult = $this->createStub(PaginatedResult::class);
        $request = $this->createStub(ServerRequestInterface::class);
        $request
            ->method("getQueryParams")
            ->willReturn([]);
        $this->twigView
            ->expects($this->once())
            ->method("render")
            ->with(
                $this->anything(),
                $this->anything(),
                $this->callback(
                    fn (array $data): bool => array_key_exists($key, $data)
                )
            );

        $this->listView->render($this->createStub(ResponseInterface::class), $paginatedResult, $request, [""], "", []);
    }

    /**
     * @return array<array{0: string}>
     */
    public static function computedDataKeyProvider(): array
    {
        return [
            ["orderingUrls"],
            ["pagination"],
        ];
    }
}
