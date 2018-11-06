<?php

require '../vendor/autoload.php';

$fryer = new MirazMac\DeepFry\Fryer('meme.jpg');
$fryer->fry()
      ->moreDeepNibba()
      ->quality(20)
      ->output();
