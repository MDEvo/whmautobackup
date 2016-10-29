<?php

// Get config
require "config.php";

require 'vendor/autoload.php';
use Mailgun\Mailgun;

$mg = new Mailgun($mail['key']);