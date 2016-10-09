<?php 
namespace cryptonator\exceptions;

class ServerError extends \Exception {
    public function __construct($error, $status_code) {
        parent::__construct("Server error: " . $error, $status_code);
    }
}