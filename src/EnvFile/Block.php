<?php

namespace EnvEditor\EnvFile;

abstract class Block {

  abstract public function visit(Visitor $visitor);

}
