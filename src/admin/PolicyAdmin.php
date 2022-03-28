<?php

namespace Toast\SimpleCSP;

use SilverStripe\Admin\ModelAdmin;
use SilverStripe\Forms\GridField\GridFieldExportButton;
use SilverStripe\Forms\GridField\GridFieldImportButton;

class PolicyAdmin extends ModelAdmin
{
    private static $url_segment = 'csp-policies';

    private static $menu_title = 'CSP Policies';

    private static $managed_models = [
        Directive::class,
        Source::class
    ];

    private static $model_importers = [
        Directive::class => DirectiveCSVBulkLoader::class
    ];    


    public function getEditForm($id = null, $fields = null)
    {
        $form = parent::getEditForm();

        $gridField = $form->Fields()->dataFieldByName($this->sanitiseClassName($this->modelClass));

        if($gridField && $gridField->exists()) {

            if ($this->modelClass == Directive::class) {
                $gridField->getConfig()
                    ->getComponentByType(GridFieldExportButton::class)
                        ->setExportColumns([
                            'Title' => 'Title',
                            'Value' => 'Value',
                            'AllowSource' => 'AllowSource',
                            'AllowUnsafeInline' => 'AllowUnsafeInline',
                            'AllowUnsafeEval' => 'AllowUnsafeEval',
                            'Enable' => 'Enable',
                            'SourcesForExport' => 'SourceList'
                        ]);
            }

            if ($this->modelClass == Source::class) {
                $gridField->getConfig()
                    ->removeComponentsByType(GridFieldExportButton::class)
                    ->removeComponentsByType(GridFieldImportButton::class);
            }

        }
        return $form;
    }


}
