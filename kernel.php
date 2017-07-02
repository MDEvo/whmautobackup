<?php

// Require
require 'vendor/autoload.php';
use Mailgun\Mailgun;

// Get config
require "app/config.php";

// Get functions
require "app/functions.php";

if (!empty($mail)) {
  $mg = new Mailgun($mail['key']);
}
