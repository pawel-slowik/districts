<?php

declare(strict_types=1);

namespace Districts\Test\Integration;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\FieldMapping;

final readonly class RemoveCollationListener
{
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
    {
        $classMetadata = $eventArgs->getClassMetadata();
        foreach ($classMetadata->fieldMappings as $fieldName => $fieldMapping) {
            $modifiedFieldMapping = $this->removeCollation($fieldMapping);
            if ($modifiedFieldMapping === null) {
                continue;
            }
            $classMetadata->setAttributeOverride($fieldName, (array) $modifiedFieldMapping);
        }
    }

    private static function removeCollation(FieldMapping $fieldMapping): ?FieldMapping
    {
        if ($fieldMapping->options === null) {
            return null;
        }
        if (!array_key_exists("collation", $fieldMapping->options)) {
            return null;
        }
        $modifiedFieldMapping = clone $fieldMapping;
        unset($modifiedFieldMapping->options["collation"]);
        return $modifiedFieldMapping;
    }
}
