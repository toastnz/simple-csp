<?php

namespace Toast\SimpleCSP;

class SimpleCSPHelper
{
    public static function getPolicyValue()
    {
        $directives = Directive::get()->filter('Enable', true);
        
        if ($directives->count() === 0) {
            return null;
        }

        $allDirectives = [];

        foreach ($directives as $directive) {
            $sources = self::getDirectiveSources($directive);
            $allDirectives[$directive->Value] = $sources;
        }

        $defaultSources = isset($allDirectives['default-src']) ? $allDirectives['default-src'] : [];
        unset($allDirectives['default-src']);

        $csp = [];

        if (!empty($defaultSources)) {
            $csp[] = 'default-src ' . implode(' ', $defaultSources);
        }

        foreach ($allDirectives as $directiveName => $sources) {
            $mergedSources = array_unique(array_merge($defaultSources, $sources));
            
            $uniqueSources = array_diff($sources, $defaultSources);
            
            if (empty($uniqueSources)) {
                continue;
            }
            
            $csp[] = $directiveName . ' ' . implode(' ', $mergedSources);
        }

        return implode('; ', $csp);
    }

    private static function getDirectiveSources($directive)
    {
        $sources = [];

        if ($directive->AllowSource) {
            if ($directive->AllowSource === '*') {
                $sources[] = '*';
            } else {
                $sources[] = '\'' . $directive->AllowSource . '\'';
            }
        }

        foreach ($directive->Sources() as $source) {
            if (trim($source->Value)) {
                $sources[] = $source->Value;
                
                if ($source->SubdomainWildcard) {
                    $sources[] = '*.' . $source->Value;
                }
            }
        }

        if ($directive->AllowUnsafeInline) {
            $sources[] = '\'unsafe-inline\'';
        }

        if ($directive->AllowUnsafeEval) {
            $sources[] = '\'unsafe-eval\'';
        }

        return array_unique($sources);
    }
}