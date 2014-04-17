<?php

namespace Hideks;

class Logger {
    
    public static function log(array $options) {
        $date = date("Y-m-d");

        $date = explode("-", $date);
        
        $folder = ( isset($options['folder']) ) ? "{$options['folder']}/" : '';
        
        $options = array(
            'title'     => ( isset($options['title']) ) ? $options['title'] : '',
            'content'   => $options['content'],
            'name'      => ( isset($options['name']) ) ? "{$options['name']}-" : '',
            'format'    => ( isset($options['format']) ) ? $options['format'] : 'text',
            'path'      => "logs/$folder{$date[0]}/{$date[1]}/$date[2]"
        );
        

        if( ! is_dir($options['path']) ){
            mkdir($options['path'] ,0777, true) or die("O script não possui permissões para criar pastas!!");
        }
        
        switch($options['format']){
            case 'text':
                $file = fopen("{$options['path']}/{$options['name']}".date('Y.m.d').".log", 'a');
                fwrite($file, date('H:i:s') . " - {$options['title']}\n");
                fwrite($file, var_export($options['content'], true));
                fwrite($file, "\n# ----- ----- ----- ----- ----- ----- ----- #\n\n");
                fclose($file);
                break;
            case 'xml':
                $items = array();

                if( is_file("{$options['path']}/{$options['name']}.xml") ){
                    $xml = (array) simplexml_load_file("{$options['path']}/{$options['name']}.xml");

                    if( isset($xml['item0']) ){
                        foreach($xml as $item){
                            $item = (array) $item;

                            $items[] = $item;
                        }
                    } else {
                        $items[] = $xml;
                    }
                }

                $items[] = $options['content'];

                $options['name'] = empty($options['name']) ? 'log' : $options['name'];
                
                $xml = new \SimpleXMLElement("<?xml version='1.0'?><{$options['name']}></{$options['name']}>");

                self::array_to_xml($items, $xml);

                $xml->asXML("{$options['path']}/{$options['name']}.xml");
                break;
            default:
                throw new \Exception("The format: {$options['format']} is not supported!!");
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