<?php

namespace Toast\SimpleCSP;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Security\Permission;


class SiteConfigExtension extends Extension
{

    private static $db = [
        'SimpleCSPEnable' => 'Boolean',
        'SimpleCSPNonceScriptEnable' => 'Boolean',
        'SimpleCSPNonceStyleEnable' => 'Boolean',
    ];

    public function updateCMSFields(FieldList $fields)
    {

        if (Permission::check('MANAGE_CSP_SOURCES')) {
            $fields->addFieldsToTab('Root.SimpleCSP', [
                CheckboxField::create('SimpleCSPEnable', 'Enable Content-Security-Policy header/meta tag')
                    ->setDescription('Please, make sure to set up and test the policies before enabling this option to prevent legit resources from being blocked.'),
                CheckboxField::create('SimpleCSPNonceScriptEnable', 'Enable Content-Security-Policy Nonce for Scripts')
                    ->setDescription('Enables the use of nonces for inline scripts. Make sure to add the nonce attribute to inline script elements.'),
                CheckboxField::create('SimpleCSPNonceStyleEnable', 'Enable Content-Security-Policy Nonce for Styles')
                    ->setDescription('Enables the use of nonces for inline styles. Make sure to add the nonce attribute to inline style elements.'),
            ]);
        }
    }


}