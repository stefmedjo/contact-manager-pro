<?php

namespace App\Service;

class DateService {

  public function toDate($value) {
    return \DateTime::createFromFormat("Y-m-d H:i:s", $value);
  }

}