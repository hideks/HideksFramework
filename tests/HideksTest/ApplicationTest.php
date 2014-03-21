<?php

namespace Hideks;

class ApplicationTest extends \PHPUnit_Framework_TestCase {
    
    public function assertPreConditions() {
        $this->assertTrue(
            class_exists($class = 'Hideks\Application'),
            'Class not found: ' . $class
        );
    }
    
    public function testInstantiationWithArgumentsShouldWork() {
        $instante = new Application();
        
        $this->assertInstanceOf(
            'Hideks\Application',
            $instante
        );
    }
    
    public function testShouldExistsMethodRun() {
        $instance = new Application();
        
        $this->assertTrue(
            method_exists($instance, 'run'),
            'There is no method "run" in object'
        );
    }
    
}