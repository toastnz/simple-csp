<?php

namespace Toast\SimpleCSP;

use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\View\TemplateGlobalProvider;

class CSPTemplateProvider implements TemplateGlobalProvider
{
    public static function get_template_global_variables()
    {
        return [
            'CSPNonceAttr'
        ];
    }

    public static function CSPNonceAttr()
    {
        if ($nonceAttr = SimpleCSPHelper::CSPNonceAttr()) {
            return DBField::create_field('HTMLFragment', $nonceAttr);
        }
    }
}
