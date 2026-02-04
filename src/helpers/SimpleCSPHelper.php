<?php

namespace Toast\SimpleCSP;

use SilverStripe\Control\Controller;
use SilverStripe\SiteConfig\SiteConfig;

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

        $config = SiteConfig::current_site_config();

        foreach ($allDirectives as $directiveName => $sources) {
            $mergedSources = array_unique(array_merge($defaultSources, $sources));
            
            $uniqueSources = array_diff($sources, $defaultSources);
            
            if (empty($uniqueSources)) {
                continue;
            }
            
            $scriptDirectives = ['script-src', 'script-src-elem', 'script-src-attr'];
            $styleDirectives = ['style-src', 'style-src-elem', 'style-src-attr'];
            
            // Add nonce to script directives if enabled
            if ($config->SimpleCSPNonceScriptEnable && in_array($directiveName, $scriptDirectives)) {
                $nonce = self::getNonceValue();
                if ($nonce) {
                    $mergedSources[] = "'nonce-" . $nonce . "'";
                    $mergedSources[] = "'strict-dynamic'";
                    $mergedSources[] = "https:"; // Fallback for Safari/older browsers
                }
            }
            
            // Add nonce to style directives if enabled
            if ($config->SimpleCSPNonceStyleEnable && in_array($directiveName, $styleDirectives)) {
                $nonce = self::getNonceValue();
                if ($nonce) {
                    $mergedSources[] = "'nonce-" . $nonce . "'";
                }
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

    public static function getNonceValue()
    {
        try {
            if ($controller = Controller::curr()) {
                if ($request = $controller->getRequest()) {
                    if ($session = $request->getSession()) {
                        return $session->get('CSPNonce');
                    }
                }
            };
        } catch (\Throwable $e) {
        }
    }

    public static function CSPNonceAttr()
    {
        $siteConfig = SiteConfig::current_site_config();

        if ($siteConfig->SimpleCSPNonceScriptEnable || $siteConfig->SimpleCSPNonceStyleEnable) {
            return 'nonce="' . self::getNonceValue() . '"';
        }
    }

    
}