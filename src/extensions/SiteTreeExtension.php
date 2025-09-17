<?php

namespace Toast\SimpleCSP;

use SilverStripe\Core\Extension;
use SilverStripe\Core\Config\Config;
use SilverStripe\SiteConfig\SiteConfig;

class SiteTreeExtension extends Extension
{
    public function updateMetaTags(&$tagString)
    {
        if (SiteConfig::current_site_config()->SimpleCSPEnable) {
            if (Config::inst()->get('SimpleCSP', 'simple_csp_metatags')) {
                if ($headerValue = SimpleCSPHelper::getPolicyValue()) {
                    $tagString .= "\n" . '<meta http-equiv="Content-Security-Policy" content="' . $headerValue . '">';
                }
            }
        }
    }

}