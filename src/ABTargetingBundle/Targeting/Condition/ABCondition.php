<?php

namespace ABTargetingBundle\Targeting\Condition;

use Pimcore\Logger;
use Pimcore\Model\Tool\Targeting\TargetGroup;
use Pimcore\Targeting\Condition\AbstractVariableCondition;
use Pimcore\Targeting\Model\VisitorInfo;

class ABCondition extends AbstractVariableCondition
{
    private $targetGroups;

    public function __construct($targetGroups = null)
    {
        $this->targetGroups = $targetGroups;
    }

    public static function fromConfig(array $config)
    {
        $targetGroups = $config['targetGroups'] ?? null;

        // build an instance from the config as configured
        // in the admin UI
        return new self($targetGroups);
    }

    public function canMatch(): bool
    {
        return true;
    }

    public function match(VisitorInfo $visitorInfo): bool
    {
        $match = true;

        if (is_array($this->targetGroups)) {
            foreach ($this->targetGroups as $targetGroup) {
                $targetGroup = TargetGroup::getById($targetGroup);
                if ($targetGroup instanceof TargetGroup) {
                    if ($visitorInfo->hasTargetGroupAssignment($targetGroup)) {
                        $match = false;
                    }
                }
            }
        }

        return $match;
    }
}