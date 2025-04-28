<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\UI\Factory;

use Districts\Editor\UI\Factory\DistrictFilterFactory;
use Districts\Editor\UI\Factory\DistrictOrderingFactory;
use Districts\Editor\UI\Factory\ListDistrictsQueryFactory;
use Districts\Editor\UI\Factory\PaginationFactory;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface as Request;

#[CoversClass(ListDistrictsQueryFactory::class)]
final class ListDistrictsQueryFactoryTest extends TestCase
{
    private ListDistrictsQueryFactory $queryFactory;

    private DistrictOrderingFactory&MockObject $districtOrderingFactory;

    private DistrictFilterFactory&MockObject $districtFilterFactory;

    private PaginationFactory&MockObject $paginationFactory;

    private Request&Stub $request;

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
     */
    #[DataProvider('orderingParametersDataProvider')]
    public function testPassingOrderingParameters(
        array $queryParams,
        ?string $expectedColumn,
        ?string $expectedDirection
    ): void {
        $this->request->method("getQueryParams")->willReturn($queryParams);

        $this->districtOrderingFactory
            ->expects($this->once())
            ->method("createFromRequestInput")
            ->with(
                $this->identicalTo($expectedColumn),
                $this->identicalTo($expectedDirection)
            );

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
     */
    #[DataProvider('filterParametersDataProvider')]
    public function testPassingFilterParameters(
        array $queryParams,
        ?string $expectedColumn,
        ?string $expectedValue
    ): void {
        $this->request->method("getQueryParams")->willReturn($queryParams);

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
     */
    #[DataProvider('paginationParametersDataProvider')]
    public function testPassingPaginationParameters(array $queryParams, ?string $expectedPage): void
    {
        $this->request->method("getQueryParams")->willReturn($queryParams);

        $this->paginationFactory
            ->expects($this->once())
            ->method("createFromRequestInput")
            ->with(
                $this->identicalTo($expectedPage)
            );

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
        try {
            $this->queryFactory->fromDefaults();
            $exceptionThrown = false;
        } catch (InvalidArgumentException) {
            $exceptionThrown = true;
        }

        $this->assertFalse($exceptionThrown);
    }
}
