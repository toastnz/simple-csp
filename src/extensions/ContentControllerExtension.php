<?php

namespace Toast\SimpleCSP;

use SilverStripe\Core\Extension;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Config;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\ORM\FieldType\DBText;

class ContentControllerExtension extends Extension
{

    public function onBeforeInit()
    {
        if (Config::inst()->get('SimpleCSP', 'simple_csp_header')) {
            if (Controller::has_curr()) {
                if (Controller::curr() instanceof ContentController) {
                    if (SiteConfig::current_site_config()->SimpleCSPEnable) {
                        if ($headerValue = SimpleCSPHelper::getPolicyValue()) {
                            $this->owner->getResponse()->addHeader('Content-Security-Policy', $headerValue);
                        }
                    }
                }
            }
        }
    }

    public function getSimpleCSPMeta()
    {
        if (SiteConfig::current_site_config()->SimpleCSPEnable) {
            if ($headerValue = SimpleCSPHelper::getPolicyValue()) {
                $tag = '<meta http-equiv="Content-Security-Policy" content="' . $headerValue . '">';
                return DBField::create_field(DBText::class, $tag);
            }
        }
    }


}