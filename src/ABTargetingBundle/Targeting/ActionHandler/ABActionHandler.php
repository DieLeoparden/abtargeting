<?php

namespace ABTargetingBundle\Targeting\ActionHandler;

use Pimcore\Model\Tool\Targeting\Rule;
use Pimcore\Targeting\ActionHandler\ActionHandlerInterface;
use Pimcore\Targeting\ActionHandler\AssignTargetGroup;
use Pimcore\Targeting\ConditionMatcherInterface;
use Pimcore\Targeting\Storage\TargetingStorageInterface;
use Pimcore\Targeting\Model\VisitorInfo;
use Pimcore\Model\Tool\Targeting\TargetGroup;
use Symfony\Component\Yaml\Yaml;

class ABActionHandler extends AssignTargetGroup implements ActionHandlerInterface
{
    const STORAGE_KEY = 'tg';

    /**
     * @var ConditionMatcherInterface
     */
    private $conditionMatcher;

    /**
     * @var TargetingStorageInterface
     */
    private $storage;

    public function __construct(
        ConditionMatcherInterface $conditionMatcher,
        TargetingStorageInterface $storage
    )
    {
        $this->conditionMatcher = $conditionMatcher;
        $this->storage = $storage;
    }

    public function apply(VisitorInfo $visitorInfo, array $action, Rule $rule = null)
    {
        $targetGroups = $action['targetGroups'] ?? null;

        if (!is_array($targetGroups)) {
            return;
        }

        $weight = 1;
        if (isset($action['weight'])) {
            $weight = (int)$action['weight'];
            if ($weight < 1) {
                $weight = 1;
            }
        }

        $array = Yaml::parseFile(__DIR__ . '/data/target_count.yml');

        if (is_numeric($array['count'])) {
            $targetGroups_output = array();
            foreach ($targetGroups as $targetGroup) {
                $targetGroup = TargetGroup::getById($targetGroup);
                if ($targetGroup instanceof TargetGroup) {
                    if (!$targetGroup || !$targetGroup->getActive()) {
                        continue;
                    }
                    else {
                        $targetGroups_output[] = $targetGroup;
                    }
                }
            }

            if (is_array($targetGroups_output)) {

                $array = Yaml::parseFile(__DIR__ . '/data/target_count.yml');

                if (is_numeric($array['count'])) {
                    $targetGroup = $targetGroups_output[$array['count']];
                    if ($array['count'] >= sizeof($targetGroups_output) - 1) {
                        $array['count'] = 0;
                    }
                    else {
                        $array['count'] += 1;
                    }
                    $yaml = Yaml::dump($array);

                    $count = $this->storeAssignments($visitorInfo, $targetGroup, $weight);
                    $this->assignToVisitor($visitorInfo, $targetGroup, $count);

                    file_put_contents(__DIR__ . '/data/target_count.yml', $yaml);
                }
            }
        }
    }

    /**
     * Loads stored assignments from storage and applies it to visitor info
     *
     * @param VisitorInfo $visitorInfo
     */

    public function loadStoredAssignments(VisitorInfo $visitorInfo)
    {
        $data = $this->storage->get(
            $visitorInfo,
            TargetingStorageInterface::SCOPE_VISITOR,
            self::STORAGE_KEY,
            []
        );

        foreach ($data as $targetGroupId => $count) {
            $targetGroup = TargetGroup::getById($targetGroupId);
            if ($targetGroup && $targetGroup->getActive()) {
                $this->assignToVisitor($visitorInfo, $targetGroup, $count);
            }
        }
    }

    protected function storeAssignments(VisitorInfo $visitorInfo, TargetGroup $targetGroup, int $weight): int
    {
        $data = $this->storage->get(
            $visitorInfo,
            TargetingStorageInterface::SCOPE_VISITOR,
            self::STORAGE_KEY,
            []
        );

        $count = $data[$targetGroup->getId()] ?? 0;
        $count += $weight;

        $data[$targetGroup->getId()] = $count;

        $this->storage->set(
            $visitorInfo,
            TargetingStorageInterface::SCOPE_VISITOR,
            self::STORAGE_KEY,
            $data
        );

        return $count;
    }


    protected function assignToVisitor(VisitorInfo $visitorInfo, TargetGroup $targetGroup, int $count)
    {
        $threshold = (int)$targetGroup->getThreshold();

        // only assign if count reached the threshold if threshold is > 1
        if ($threshold <= 1 || $count >= $threshold) {
            $visitorInfo->assignTargetGroup($targetGroup, $count, true);
        }
    }
}