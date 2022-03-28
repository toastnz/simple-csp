<?php

namespace Toast\SimpleCSP;

use SilverStripe\Core\Extension;
use SilverStripe\Control\Controller;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\CMS\Controllers\ContentController;

class ControllerExtension extends Extension
{

    public function onBeforeInit()
    {
        if (Controller::has_curr()) {
            if (Controller::curr() instanceof ContentController) {
                if (SiteConfig::current_site_config()->SimpleCSPEnable) {
                    $this->addCSPHeader();
                }
            }
        }        
    }

    private function addCSPHeader()
    {
        $csp = [];

        $directives = Directive::get()
            ->filter('Enable', true);

        foreach ($directives as $directive) {
            $csp[] = $directive->getPolicy();
        }

        if (count($csp)) {
            $this->owner->getResponse()->addHeader('Content-Security-Policy', implode('; ', $csp));
        }
    }


}