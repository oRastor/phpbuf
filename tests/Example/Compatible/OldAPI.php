<?php
class PhpBuf_Message_Example_Compatible_OldAPI extends PhpBuf_Message_Abstract {
    public function __construct() {
        $this->setField("a", PhpBuf\Type::INT, PhpBuf\Rule::REQUIRED, 1);
    }
    public static function name(){
        return __CLASS__;
    }

}
?>
