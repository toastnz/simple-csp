<?php

namespace Toast\SimpleCSP;

use SilverStripe\Dev\Debug;
use SilverStripe\Core\Extension;
use SilverStripe\Control\Controller;
use SilverStripe\Core\Config\Config;
use SilverStripe\ORM\FieldType\DBText;
use SilverStripe\ORM\FieldType\DBField;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\CMS\Controllers\ContentController;

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
                        if ($headerValues = SimpleCSPHelper::getStandaloneHeaderPolicyValues()) {
                            foreach ($headerValues as $key => $headerValue) {
                                $this->owner->getResponse()->addHeader($key, $headerValue);
                            }
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
