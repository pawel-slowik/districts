<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\UI\Factory;

use Districts\Editor\Domain\DistrictOrdering;
use Districts\Editor\Domain\DistrictOrderingField;
use Districts\Editor\Domain\OrderingDirection;
use Districts\Editor\Domain\Pagination;
use Districts\Editor\UI\Factory\DistrictFilterFactory;
use Districts\Editor\UI\Factory\DistrictOrderingFactory;
use Districts\Editor\UI\Factory\ListDistrictsQueryFactory;
use Districts\Editor\UI\Factory\PaginationFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * @covers \Districts\Editor\UI\Factory\ListDistrictsQueryFactory
 */
class ListDistrictsQueryFactoryTest extends TestCase
{
    private ListDistrictsQueryFactory $queryFactory;

    /** @var DistrictOrderingFactory&MockObject */
    private DistrictOrderingFactory $districtOrderingFactory;

    /** @var DistrictFilterFactory&MockObject */
    private DistrictFilterFactory $districtFilterFactory;

    /** @var MockObject&PaginationFactory */
    private PaginationFactory $paginationFactory;

    /** @var Request&Stub */
    private Request $request;

    protected function setUp(): void
    {
        $this->districtOrderingFactory = $this->createMock(DistrictOrderingFactory::class);
        $this->districtFilterFactory = $this->createMock(DistrictFilterFactory::class);
        $this->paginationFactory = $this->createMock(PaginationFactory::class);
        $this->request = $this->createStub(Request::class);

        $this->queryFactory = new ListDistrictsQueryFactory(
            $this->districtOrderingFactory,
            $this->districtFilterFactory,
            $this->paginationFactory,
        );
    }

    /**
     * @param array<string, string> $queryParams
     *
     * @dataProvider orderingParametersDataProvider
     */
    public function testPassingOrderingParameters(
        array $queryParams,
        ?string $expectedColumn,
        ?string $expectedDirection
    ): void {
        $this->request->method("getQueryParams")->willReturn($queryParams);
        $this->paginationFactory
            ->method("createFromRequestInput")
            ->willReturn(new Pagination(1, 1));

        $this->districtOrderingFactory
            ->expects($this->once())
            ->method("createFromRequestInput")
            ->with(
                $this->identicalTo($expectedColumn),
                $this->identicalTo($expectedDirection)
            )
            ->willReturn(new DistrictOrdering(DistrictOrderingField::FullName, OrderingDirection::Asc));

        $this->queryFactory->fromRequest($this->request);
    }

    /**
     * @return array<array{0: array<int|string, string>, 1: ?string, 2: ?string}>
     */
    public static function orderingParametersDataProvider(): array
    {
        return [
            [
                [
                    "orderColumn" => "foo",
                    "orderDirection" => "bar",
                ],
                "foo",
                "bar",
            ],
            [
                [
                    "orderColumn" => "foo",
                ],
                "foo",
                null,
            ],
            [
                [
                    "orderDirection" => "bar",
                ],
                null,
                "bar",
            ],
            [
                [],
                null,
                null,
            ],
            [
                [
                    "qux1" => "foo",
                    "qux2" => "bar",
                ],
                null,
                null,
            ],
            [
                [
                    "foo",
                    "bar",
                ],
                null,
                null,
            ],
        ];
    }

    /**
     * @param array<string, string> $queryParams
     *
     * @dataProvider filterParametersDataProvider
     */
    public function testPassingFilterParameters(
        array $queryParams,
        ?string $expectedColumn,
        ?string $expectedValue
    ): void {
        $this->request->method("getQueryParams")->willReturn($queryParams);
        $this->districtOrderingFactory
            ->method("createFromRequestInput")
            ->willReturn(new DistrictOrdering(DistrictOrderingField::FullName, OrderingDirection::Asc));
        $this->paginationFactory
            ->method("createFromRequestInput")
            ->willReturn(new Pagination(1, 1));

        $this->districtFilterFactory
            ->expects($this->once())
            ->method("createFromRequestInput")
            ->with(
                $this->identicalTo($expectedColumn),
                $this->identicalTo($expectedValue)
            );

        $this->queryFactory->fromRequest($this->request);
    }

    /**
     * @return array<array{0: array<int|string, string>, 1: ?string, 2: ?string}>
     */
    public static function filterParametersDataProvider(): array
    {
        return [
            [
                [
                    "filterColumn" => "foo",
                    "filterValue" => "bar",
                ],
                "foo",
                "bar",
            ],
            [
                [
                    "filterColumn" => "foo",
                ],
                "foo",
                null,
            ],
            [
                [
                    "filterValue" => "bar",
                ],
                null,
                "bar",
            ],
            [
                [],
                null,
                null,
            ],
            [
                [
                    "qux1" => "foo",
                    "qux2" => "bar",
                ],
                null,
                null,
            ],
            [
                [
                    "foo",
                    "bar",
                ],
                null,
                null,
            ],
        ];
    }

    /**
     * @param array<string, string> $queryParams
     *
     * @dataProvider paginationParametersDataProvider
     */
    public function testPassingPaginationParameters(array $queryParams, ?string $expectedPage): void
    {
        $this->request->method("getQueryParams")->willReturn($queryParams);
        $this->districtOrderingFactory
            ->method("createFromRequestInput")
            ->willReturn(new DistrictOrdering(DistrictOrderingField::FullName, OrderingDirection::Asc));

        $this->paginationFactory
            ->expects($this->once())
            ->method("createFromRequestInput")
            ->with(
                $this->identicalTo($expectedPage)
            )
            ->willReturn(new Pagination(1, 1));

        $this->queryFactory->fromRequest($this->request);
    }

    /**
     * @return array<array{0: array<int|string, string>, 1: ?string}>
     */
    public static function paginationParametersDataProvider(): array
    {
        return [
            [
                [
                    "page" => "foo",
                ],
                "foo",
            ],
            [
                [],
                null,
            ],
            [
                [
                    "qux1" => "foo",
                ],
                null,
            ],
            [
                [
                    "foo",
                ],
                null,
            ],
        ];
    }

    public function testDefaults(): void
    {
        $this->districtOrderingFactory
            ->method("createFromRequestInput")
            ->willReturn(new DistrictOrdering(DistrictOrderingField::FullName, OrderingDirection::Asc));
        $this->paginationFactory
            ->method("createFromRequestInput")
            ->willReturn(new Pagination(1, 1));

        $this->queryFactory->fromDefaults();

        $this->assertTrue(true);
    }
}
