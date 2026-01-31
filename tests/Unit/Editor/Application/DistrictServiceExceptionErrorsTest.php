<?php

declare(strict_types=1);

namespace Districts\Test\Unit\Editor\Application;

use Districts\Core\Domain\CityRepository;
use Districts\Editor\Application\Command\AddDistrictCommand;
use Districts\Editor\Application\Command\UpdateDistrictCommand;
use Districts\Editor\Application\DistrictService;
use Districts\Editor\Application\DistrictValidator;
use Districts\Editor\Application\Exception\ValidationException;
use Districts\Editor\Application\ValidationResult;
use Districts\Editor\Domain\DistrictRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

#[CoversClass(DistrictService::class)]
final class DistrictServiceExceptionErrorsTest extends TestCase
{
    private DistrictService $districtService;

    private DistrictRepository&Stub $districtRepository;

    private DistrictValidator&Stub $districtValidator;

    private CityRepository&Stub $cityRepository;

    protected function setUp(): void
    {
        $this->districtRepository = $this->createStub(DistrictRepository::class);
        $this->districtValidator = $this->createStub(DistrictValidator::class);
        $this->cityRepository = $this->createStub(CityRepository::class);

        $this->districtService = new DistrictService(
            $this->districtValidator,
            $this->districtRepository,
            $this->cityRepository,
        );
    }

    public function testAddExceptionErrors(): void
    {
        $command = $this->createStub(AddDistrictCommand::class);

        $this->districtValidator
            ->method("validateAdd")
            ->willReturn($this->createValidationErrorStub());

        try {
            $this->districtService->add($command);
        } catch (ValidationException $exception) {
            $this->assertContains("foo", $exception->getErrors());
        }
    }

    public function testUpdateExceptionErrors(): void
    {
        $command = $this->createStub(UpdateDistrictCommand::class);

        $this->districtValidator
            ->method("validateUpdate")
            ->willReturn($this->createValidationErrorStub());

        try {
            $this->districtService->update($command);
        } catch (ValidationException $exception) {
            $this->assertContains("foo", $exception->getErrors());
        }
    }

    private function createValidationErrorStub(): ValidationResult
    {
        $validationResult = $this->createStub(ValidationResult::class);
        $validationResult
            ->method("isOk")
            ->willReturn(false);
        $validationResult
            ->method("getErrors")
            ->willReturn(["foo"]);

        return $validationResult;
    }
}
