<?php

namespace Toast\SimpleCSP;

use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Security\Permission;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\Security\PermissionProvider;

class Source extends DataObject implements PermissionProvider
{
    private static $table_name = 'SimpleCSP_Source';

    private static $db = [
        'Title' => 'Varchar(255)',
        'Value' => 'Varchar(255)',
        'SubdomainWildcard' => 'Boolean'
    ];

    private static $belongs_many_many = [
        'Directives' => Directive::class
    ];

    private static $default_sort = 'Title';

    private static $summary_fields = [
        'Title' => 'Title',
        'Value' => 'Value',
        'SubdomainWildcard.Nice' => 'Also allow subdomains'
    ];

    private static $searchable_fields = [
        'Title',
        'Value'
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName('Directives');

        $fields->addFieldsToTab('Root.Main', [
            TextField::create('Title', 'Friendly name'),
            TextField::create('Value', 'Source value')
                ->setDescription('Refer to https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP for more information'),
            CheckboxField::create('SubdomainWildcard', 'Also allow subdomains')
                ->setDescription('Important: Use only if value is a domain name since this will prepend a wildcard to the source value, e.g. \'*.example.com\''),
            CheckboxSetField::create('Directives', 'Directives', Directive::get()->map())
        ]);

        return $fields;
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        if (!$this->Title) {
            $this->Title = $this->Value;
        }
    }

    public function getCMSValidator()
    {
        return RequiredFields::create([
            'Value'
        ]);
    }

    public function providePermissions()
    {
        return [
            'MANAGE_CSP_SOURCES' => 'Manage Content-Security-Policy sources'
        ];
    }

    public function canCreate($member = null, $context = [])
    {
        return Permission::check('MANAGE_CSP_SOURCES');
    }

    public function canEdit($member = null)
    {
        return Permission::check('MANAGE_CSP_SOURCES');
    }

    public function canView($member = null)
    {
        return Permission::check('MANAGE_CSP_SOURCES');
    }

    public function canDelete($member = null)
    {
        return Permission::check('MANAGE_CSP_SOURCES');
    }

}