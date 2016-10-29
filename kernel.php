<?php

// Get config
require "app/config.php";

require 'vendor/autoload.php';
use Mailgun\Mailgun;

$mg = new Mailgun($mail['key']);