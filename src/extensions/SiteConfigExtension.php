<?php

namespace Toast\SimpleCSP;

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Security\Permission;


class SiteConfigExtension extends DataExtension
{

    private static $db = [
        'SimpleCSPEnable' => 'Boolean'
    ];

    public function updateCMSFields(FieldList $fields)
    {

        if (Permission::check('MANAGE_CSP_SOURCES')) {
            $fields->addFieldsToTab('Root.SimpleCSP', [
                CheckboxField::create('SimpleCSPEnable', 'Enable Content-Security-Policy header/meta tag')
                    ->setDescription('Please, make sure to set up and test the policies before enabling this option to prevent legit resources from being blocked.')
            ]);
        }
    }


}