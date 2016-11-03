<?php /** @copyright Alejandro Salazar (c) 2016 */

// Helper script to run the Config Generator

require '../vendor/autoload.php';

$configGenerator = new \Shipping\ZoneChart\ConfigGenerator();
$configGenerator->generate();