<?php

namespace Toast\SimpleCSP;

use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Security\Permission;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\ORM\FieldType\DBHTMLText;
use SilverStripe\Security\PermissionProvider;

class Directive extends DataObject implements PermissionProvider
{
    private static $table_name = 'SimpleCSP_Directive';

    private static $db = [
        'Title' => 'Varchar(255)',
        'Value' => 'Varchar(255)',
        'AllowSource' => 'Enum("*,self,none", "*")',
        'AllowUnsafeInline' => 'Boolean',
        'AllowUnsafeEval' => 'Boolean',
        'Enable' => 'Boolean',
        'isStandaloneHeader' => 'Boolean'
    ];

    private static $many_many = [
        'Sources' => Source::class
    ];

    private static $summary_fields = [
        'Title' => 'Title',
        'PolicyForSummary' => 'Policy'
    ];

    private static $searchable_fields = [
        'Title',
        'Value',
        'AllowSource',
        'AllowUnsafeInline',
        'AllowUnsafeEval'
    ];

    private static $default_sort = 'Title';

    private static $default_directives = [
        'base-uri',
        'child-src',
        'connect-src',
        'default-src',
        'font-src',
        'form-action',
        'frame-ancestors',
        'frame-src',
        'img-src',
        'manifest-src',
        'media-src',
        'navigate-to',
        'object-src',
        'plugin-types',
        'prefetch-src',
        'referrer',
        'report-to',
        'report-uri',
        'require-sri-for',
        'require-trusted-types-for',
        'sandbox',
        'script-src-attr',
        'script-src-elem',
        'script-src',
        'style-src-attr',
        'style-src-elem',
        'style-src',
        'trusted-types',
        'upgrade-insecure-requests',
        'worker-src'        
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName('Sources');

        $fields->addFieldsToTab('Root.Main', [
            TextField::create('Title', 'Friendly name'),
            TextField::create('Value', 'Directive value')
                ->setDescription('Refer to https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy for more information'),
            OptionsetField::create('AllowSource', 'Default source', [
                '*' => '*',
                'self' => 'self',
                'none' => 'none'
            ]),
            CheckboxField::create('AllowUnsafeInline', 'Allow \'unsafe-inline\''),
            CheckboxField::create('AllowUnsafeEval', 'Allow \'unsafe-eval\''),
            CheckboxField::create('Enable', 'Enable directive'),
            CheckboxSetField::create('Sources', 'Sources', Source::get()->map()),
            CheckboxField::create('isStandaloneHeader', 'Allows this Source to be a custom header')
                ->setDescription('Important: Use only if your sure of the header type and values'),
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

    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();

        foreach(self::$default_directives as $value) {
            if (!self::get()->find('Value', $value)) {
                $directive = new Directive;
                $directive->Value = $value;
                $directive->write();
            }
        }
    }

    public function getPolicy()
    {
        $policy = $this->Value;

        if ($this->AllowSource) {

            if ($this->AllowSource == '*') {
                $policy .= ' *';
            } else {
                $policy .= ' ' . '\'' . $this->AllowSource . '\'';
            }
        }

        foreach($this->Sources() as $source) {
            if (trim($source->Value)) {
                $policy .= ' ' . $source->Value;

                if ($source->SubdomainWildcard) {
                    $policy .= ' *.' . $source->Value;
                }
            }
        }

        if ($this->AllowUnsafeInline) {
            $policy .= ' ' . '\'unsafe-inline\'';
        }

        if ($this->AllowUnsafeEval) {
            $policy .= ' ' . '\'unsafe-eval\'';
        }

        return $policy;
    }

    public function getOnlyDirectivePolicy()
    {
        $policy = '';

        if ($this->AllowSource) {

            if ($this->AllowSource == '*') {
                $policy .= ' *';
            } else {
                $policy .= ' ' . '\'' . $this->AllowSource . '\'';
            }
        }

        foreach($this->Sources() as $source) {
            if (trim($source->Value)) {
                $policy .= ' ' . $source->Value;

                if ($source->SubdomainWildcard) {
                    $policy .= ' *.' . $source->Value;
                }
            }
        }

        if ($this->AllowUnsafeInline) {
            $policy .= ' ' . '\'unsafe-inline\'';
        }

        if ($this->AllowUnsafeEval) {
            $policy .= ' ' . '\'unsafe-eval\'';
        }

        return $policy;
    }

    public function getPolicyForSummary()
    {
        return DBField::create_field(DBHTMLText::class, '
            <span style="opacity: ' . ($this->Enable ? '1' : '0.5') . '; background-color: ' . ($this->Enable ? '#d9ffe7' : '#fff') . '; display: inline-block; font-family: monospace; padding: 2px 10px; border: 1px solid #e0e0e0; border-radius: 2px;">' . $this->getPolicy() . '</span>
        ');
    }

    public function getSourcesForExport()
    {
        $sources = [];

        foreach($this->Sources() as $source) {
            $sources[] = $source->Value;

            if ($source->SubdomainWildcard) {
                $sources[] = '*.' . $source->Value;
            }
        }

        return implode(', ', $sources);
    }

    public function providePermissions()
    {
        return [
            'MANAGE_CSP_DIRECTIVES' => 'Manage Content-Security-Policy directives'
        ];
    }

    public function canCreate($member = null, $context = [])
    {
        return Permission::check('MANAGE_CSP_DIRECTIVES');
    }

    public function canEdit($member = null)
    {
        return Permission::check('MANAGE_CSP_DIRECTIVES');
    }

    public function canView($member = null)
    {
        return Permission::check('MANAGE_CSP_DIRECTIVES');
    }

    public function canDelete($member = null)
    {
        return Permission::check('MANAGE_CSP_DIRECTIVES');
    }

}
