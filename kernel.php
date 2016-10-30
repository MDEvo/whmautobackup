<?php

// Get config
require "app/config.php";

// Get functions
require "app/functions.php";

require 'vendor/autoload.php';
use Mailgun\Mailgun;

$mg = new Mailgun($mail['key']);