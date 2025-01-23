<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\UI\View;

use Districts\Editor\Domain\District;
use Districts\Editor\Domain\PaginatedResult;
use Districts\Editor\Domain\Pagination;
use Districts\Editor\UI\View\ListView;
use Districts\Editor\UI\View\OrderingUrlGenerator;
use Districts\Editor\UI\View\PageReferenceFactory;
use Nyholm\Psr7\Uri;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;
use Slim\Views\Twig as TwigView;

#[CoversClass(ListView::class)]
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

    /** @var Stub&UriFactoryInterface */
    private UriFactoryInterface $uriFactory;

    protected function setUp(): void
    {
        $this->twigView = $this->createMock(TwigView::class);
        $this->pageReferenceFactory = $this->createStub(PageReferenceFactory::class);
        $this->orderingUrlGenerator = $this->createStub(OrderingUrlGenerator::class);
        $this->uriFactory = $this->createStub(UriFactoryInterface::class);
        $this->uriFactory->method("createUri")->willReturn(new Uri());

        $this->listView = new ListView(
            $this->twigView,
            $this->pageReferenceFactory,
            $this->orderingUrlGenerator,
            $this->uriFactory,
        );
    }

    public function testRenders(): void
    {
        $paginatedResult = new PaginatedResult(new Pagination(1, 1), 1, 1, []);
        $request = $this->createStub(ServerRequestInterface::class);
        $requestUri = $this->createStub(UriInterface::class);
        $requestUri
            ->method("getPath")
            ->willReturn("/");
        $request
            ->method("getUri")
            ->willReturn($requestUri);

        $this->twigView
            ->expects($this->once())
            ->method("render");

        $this->listView->render($this->createStub(ResponseInterface::class), $paginatedResult, $request, [], "", []);
    }

    #[DataProvider('computedDataKeyProvider')]
    public function testSetsComputedDataKey(string $key): void
    {
        $paginatedResult = new PaginatedResult(new Pagination(1, 1), 1, 1, []);
        $request = $this->createStub(ServerRequestInterface::class);
        $requestUri = $this->createStub(UriInterface::class);
        $requestUri
            ->method("getPath")
            ->willReturn("/");
        $request
            ->method("getUri")
            ->willReturn($requestUri);

        $this->twigView
            ->expects($this->once())
            ->method("render")
            ->with(
                $this->anything(),
                $this->anything(),
                $this->callback(
                    static fn (array $data): bool => array_key_exists($key, $data)
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
            ["entries"],
            ["orderingUrls"],
            ["pagination"],
        ];
    }
}
