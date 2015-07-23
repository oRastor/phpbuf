<?php
class PhpBuf_Message_Example_Compatible_NewAPI extends PhpBuf_Message_Abstract {
    public function __construct() {
        $this->setField("a", PhpBuf\Type::INT, PhpBuf\Rule::REQUIRED, 1);
        $this->setField("b", PhpBuf\Type::STRING, PhpBuf\Rule::REQUIRED, 2);
    }
    public static function name(){
        return __CLASS__;
    }

}
?>
