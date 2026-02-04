<?php

namespace Toast\SimpleCSP;

use SilverStripe\Core\Extension;
use SilverStripe\Core\Config\Config;
use SilverStripe\ORM\FieldType\DBText;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\SiteConfig\SiteConfig;

class ContentControllerExtension extends Extension
{

    public function onBeforeInit()
    {
        $this->owner->getRequest()->getSession()->set('CSPNonce', bin2hex(random_bytes(16)));

        if (SiteConfig::current_site_config()->SimpleCSPEnable) {
            if (Config::inst()->get('SimpleCSP', 'simple_csp_headers')) {    
                if ($headerValue = SimpleCSPHelper::getPolicyValue()) {
                    $this->owner->getResponse()->addHeader('Content-Security-Policy', $headerValue);
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