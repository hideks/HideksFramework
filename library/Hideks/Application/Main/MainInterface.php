<?php

namespace Hideks\Application\Main;

interface MainInterface {
    
    public function __construct($application);
    
    public function main();
    
    public function run();
    
}