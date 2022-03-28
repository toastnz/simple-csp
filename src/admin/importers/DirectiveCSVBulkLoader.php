<?php

namespace Toast\SimpleCSP;

use SilverStripe\Dev\CsvBulkLoader;


class DirectiveCSVBulkLoader extends CsvBulkLoader 
{

   public $columnMap = [
      'Title' => 'Title',
      'Value' => 'Value',
      'AllowSource' => 'AllowSource',
      'AllowUnsafeInline' => 'AllowUnsafeInline',
      'AllowUnsafeEval' => 'AllowUnsafeEval',
      'Enable' => 'Enable',
      'SourceList' => '->setSources'
   ];


   public static function setSources(&$obj, $val, $record) 
   {
      if (trim($val)) {
         $sourceValues = explode(',', $val);

         foreach($sourceValues as $sourceValue) {
            $sourceValue = trim($sourceValue);
            $isSubdomainWildcard = substr($sourceValue, 0, 2) == '*.';
            $sourceValue = str_replace('*.', '', $sourceValue);

            $source = Source::get()
               ->find('Value', $sourceValue);

            if (!$source) {
               $source = new Source;
               $source->Value = $sourceValue;
            }

            $source->SubdomainWildcard = $isSubdomainWildcard;
            $source->write();

            $obj->Sources()->add($source);
         }
      }

   }


}



