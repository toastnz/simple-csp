<?php

namespace Toast\SimpleCSP;

class SimpleCSPHelper
{

    public static function  getPolicyValue()
    {
        $csp = [];

        $directives = Directive::get()
            ->filter([
                'Enable' => true,
                'isStandaloneHeader' => false
            ]);

        foreach ($directives as $directive) {
            $csp[] = $directive->getPolicy();
        }

        if (count($csp)) {
            return implode('; ', $csp);
        }
    }

    public static function  getStandaloneHeaderPolicyValues()
    {
        $csp = [];

        $directives = Directive::get()
            ->filter([
                'Enable' => true,
                'isStandaloneHeader' => true
            ]);
        foreach ($directives as $directive) {
            

            $csp[$directive->Title] = $directive->getOnlyDirectivePolicy();
        }

        if (count($csp)) {
            return $csp;
        }
    }



}
