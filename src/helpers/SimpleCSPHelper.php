<?php

namespace Toast\SimpleCSP;

class SimpleCSPHelper
{

    public static function  getPolicyValue()
    {
        $csp = [];

        $directives = Directive::get()
            ->filter('Enable', true);

        foreach ($directives as $directive) {
            $csp[] = $directive->getPolicy();
        }

        if (count($csp)) {
            return implode('; ', $csp);
        }
    }



}