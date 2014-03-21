<?php

namespace Hideks\Controller;

class Helper {
    
    public static function formatAntiSqlInjection($string){
        $string = (get_magic_quotes_gpc() === 0) ? addslashes($string) : $string;
        $string = trim($string);
        $string = strip_tags($string);
        return $string;
    }
    
    public static function formatNames(array $names, $column) {
        $count = count($names);
        
        if ($count > 0) {
            if ($count > 2) {
                $namesArr = array();
                
                for ($j = 0; $j <= $count - 2; $j++) {
                    $namesArr[] = $names[$j][$column];
                }
                
                $comma = implode(", ", $namesArr);
                
                $names = $comma . ' e ' . $names[$count - 1][$column];
            } else {
                if ($count > 1) {
                    $names = $names[0][$column] . ' e ' . $names[1][$column];
                } else {
                    $names = $names[0][$column];
                }
            }
        } else {
            $names = "";
        }
        
        return $names;
    }
    
    static function formatSeo($string) {
        $string = strtr($string,"ÁÃÂÀÄÉÊÈËÍÎÌÏÓÕÔÒÖÚÛÙÜÑÇ","áãâàäéêèëíîìïóõôòöúûùüñç");
        $string = utf8_encode($string);
	$string = preg_replace("[Ã¡|Ã£|Ã¢|Ã |Ã¥|Ã¤] i", "a", $string);
	$string = preg_replace("[Ã©|Ãª|Ã¨|Ã«] i", "e", $string);
	$string = preg_replace("[Ã­|Ã¬|Ã®|Ã¯] i", "i", $string);
	$string = preg_replace("[Ã³|Ãµ|Ã´|Ã²|Ã¶] i", "o", $string);
	$string = preg_replace("[Ãº|Ã»|Ã¹|Ã¼] i", "u", $string);
	$string = preg_replace("[Ã±] i", "n", $string);
	$string = preg_replace("[Ã§] i", "c", $string);
	$string = preg_replace("`\[.*\]`U", "", $string);
	$string = preg_replace('`&(amp;)?#?[a-z0-9]+;`i', '-', $string);
	$string = preg_replace("/\d/", "", $string);
        $string = preg_replace("`&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);`i", "\\1", $string);
	$string = preg_replace(array("`[^a-z0-9]`i", "`[-]+`"), "-", $string);
        return strtolower(trim($string, '-'));
    }
    
    static function createLog(array $options) {
        $folder     = $options['folder'];
        $title      = $options['title'];
        $content    = $options['content'];
        $name       = ( isset($options['name']) ) ? "{$options['name']}-" : "";
        $format     = ( isset($options['format']) ) ? $options['format'] : "text";
        
        $date = date("Y-m-d");

        $date = explode("-", $date);

        $path = "logs/{$folder}/{$date[0]}/{$date[1]}/$date[2]";

        if( ! is_dir($path) ){
            mkdir($path ,0777, true) or die("O script não possui permissões para criar pastas!!");
        }
        
        switch($format){
            case "text":
                $file = fopen("{$path}/{$name}".date('Y.m.d').".log", 'a');
                fwrite($file, date('H:i:s') . " - $title\n");
                fwrite($file, var_export($content, true));
                fwrite($file, "\n# ----- ----- ----- ----- ----- ----- ----- #\n\n");
                fclose($file);
                break;
            case "xml":
                $items = array();
                
                if( is_file("{$path}/{$options['name']}.xml") ){
                    $xml = (array) simplexml_load_file("{$path}/{$options['name']}.xml");
                    
                    if( isset($xml['item0']) ){
                        foreach($xml as $item){
                            $item = (array) $item;
                            
                            $items[] = $item;
                        }
                    } else {
                        $items[] = $xml;
                    }
                }
                    
                $items[] = $content;

                $xml = new SimpleXMLElement("<?xml version='1.0'?><{$options['name']}></{$options['name']}>");

                self::array_to_xml($items, $xml);

                $xml->asXML("{$path}/{$options['name']}.xml");
                break;
            default: throw new Hideks_Function_Exception("Formato de log não suportado!!", "");
        }
    }
    
    private static function array_to_xml($content, &$xml) {
        foreach($content as $key => $value) {
            if(is_array($value)) {
                if( !is_numeric($key) ){
                    $subnode = $xml->addChild("$key");
                    self::array_to_xml($value, $subnode);
                } else {
                    $subnode = $xml->addChild("item$key");
                    self::array_to_xml($value, $subnode);
                }
            } else {
                $xml->addChild("$key","$value");
            }
        }
    }
    
}