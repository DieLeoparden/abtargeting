<?php

namespace ABTargetingBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;

class ABTargetingBundle extends AbstractPimcoreBundle
{
    use PackageVersionTrait;

    const PACKAGE_NAME = 'leoparden/abtargeting';

    public function getJsPaths()
    {
        return [
            '/bundles/abtargeting/js/targeting/condition.js',
            '/bundles/abtargeting/js/targeting/actionhandler.js'
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getComposerPackageName(): string
    {
        return self::PACKAGE_NAME;
    }
}
