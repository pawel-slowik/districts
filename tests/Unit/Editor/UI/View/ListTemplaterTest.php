<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\UI\View;

use Districts\Editor\Domain\District;
use Districts\Editor\Domain\PaginatedResult;
use Districts\Editor\Domain\Pagination;
use Districts\Editor\UI\View\ListTemplater;
use Districts\Editor\UI\View\OrderingUrlGenerator;
use Districts\Editor\UI\View\PageReferenceFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriFactoryInterface;

#[CoversClass(ListTemplater::class)]
class ListTemplaterTest extends TestCase
{
    /** @var ListTemplater<District> */
    private ListTemplater $listTemplater;

    /** @var PageReferenceFactory&Stub */
    private PageReferenceFactory $pageReferenceFactory;

    /** @var OrderingUrlGenerator&Stub */
    private OrderingUrlGenerator $orderingUrlGenerator;

    /** @var Stub&UriFactoryInterface */
    private UriFactoryInterface $uriFactory;

    protected function setUp(): void
    {
        $this->pageReferenceFactory = $this->createStub(PageReferenceFactory::class);
        $this->orderingUrlGenerator = $this->createStub(OrderingUrlGenerator::class);
        $this->uriFactory = $this->createStub(UriFactoryInterface::class);

        $this->listTemplater = new ListTemplater(
            $this->pageReferenceFactory,
            $this->orderingUrlGenerator,
            $this->uriFactory,
        );
    }

    #[DataProvider('computedDataKeyProvider')]
    public function testSetsComputedDataKey(string $key): void
    {
        $paginatedResult = new PaginatedResult(new Pagination(1, 1), 1, 1, []);
        $request = $this->createStub(ServerRequestInterface::class);

        $templateData = $this->listTemplater->prepareTemplateData(
            $paginatedResult,
            $request,
            [],
            "",
            null,
            null,
        );

        $this->assertArrayHasKey($key, $templateData);
    }

    /**
     * @return array<array{0: string}>
     */
    public static function computedDataKeyProvider(): array
    {
        return [
            ["title"],
            ["successMessage"],
            ["errorMessage"],
            ["entries"],
            ["orderingUrls"],
            ["pagination"],
            ["filter"],
        ];
    }
}
