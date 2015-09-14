<?php

// Initialize router
$router = new \Neuron\Router ();

$broodModule = new Module();

// Make the module available on /account
$router->module ('/', $broodModule);

return $router;