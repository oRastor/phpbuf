<?php

namespace PhpBuf\Field;

use PhpBuf\IO\Reader\ReaderInterface,
    PhpBuf\IO\Writer\WriterInterface,
    PhpBuf\WireType,
    PhpBuf\Rule,
    PhpBuf\Base128;

/**
 * @author Andrey Lepeshkin (lilipoper@gmail.com)
 * @link http://github.com/undr/phpbuf
 *
 */
abstract class AbstractField implements FieldInterface
{

    /**
     * Value of field
     *
     * @var mixed
     */
    protected $value = null;

    /**
     * Additional information for field.
     * If field has enum type, then extra contain array of enumerable values
     * If field has message type, then extra contain name of message class as string
     *
     * @var mixed
     */
    protected $extra;

    /**
     * Has 1, 2 or 3. PhpBuf\Rule::REQUIRED, PhpBuf\Rule::OPTIONAL, PhpBuf\Rule::REPEATED
     *
     * @var integer
     */
    protected $rule;

    /**
     * Index of field tag
     *
     * @var integer
     */
    protected $index;

    /**
     * Wire type
     *
     * @var integer
     */
    protected $wireType;

    /**
     * Fabric method, create classes extended from PhpBuf\Field\AbstractField
     *
     * @param string $type
     * @param array $args
     * @return PhpBuf\Field\AbstractField
     */
    public static function create($type, $args)
    {
        $class = '\PhpBuf\Field\\' . \PhpBuf\Type::getNameById($type);
        if (!class_exists($class)) {
            throw new \PhpBuf\Field\Exception("field '$class' not found");
        }

        return new $class($args['index'], $args['rule'], $args['extra']);
    }

    /**
     * Constructor. Возможно его нужно закрыть
     *
     * @param integer $index
     * @param integer $rule
     * @param mixed $extra
     */
    public function __construct($index, $rule, $extra)
    {
        $this->index = $index;
        $this->rule = $rule;
        $this->extra = $extra;
    }

    /**
     * To set value of field
     *
     * @param mixed $value
     */
    public function setValue($value)
    {
        if (!$this->checkTypeOfValue($value)) {
            throw new PhpBuf\Field\Exception("wrong type of value (value type: " . gettype($value) . ")");
        }
        $this->value = $value;
    }

    /**
     * To get value of field
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function getRule()
    {
        return $this->rule;
    }

    public function getExtra()
    {
        return $this->extra;
    }

    public function getIndex()
    {
        return $this->index;
    }

    /**
     * Read field from reader
     *
     * @param PhpBuf\IO\Reader\ReaderInterface $reader
     */
    public function read(ReaderInterface $reader)
    {
        if (Rule::REPEATED === $this->rule) {
            $this->value[] = $this->readImpl($reader);
        } else {
            $this->value = $this->readImpl($reader);
        }
    }

    /**
     * Write field to writer
     *
     * @param PhpBuf\IO\Writer\Interface $writer
     */
    public function write(WriterInterface $writer)
    {
        if (Rule::OPTIONAL === $this->rule && null === $this->value) {
            return;
        }

        if (Rule::REPEATED === $this->rule) {
            if (null === $this->value) {
                return;
            }
            foreach ($this->value as $item) {
                $this->writeHeader($writer);
                $this->writeImpl($writer, $item);
            }
        } else {
            $this->writeHeader($writer);
            $this->writeImpl($writer, $this->value);
        }
    }

    /**
     * Enter description here...
     *
     * @return integer
     */
    public function getWireType()
    {
        return $this->wireType;
    }

    /**
     * Enter description here...
     *
     * @param PhpBuf\IO\Reader\ReaderInterface $reader
     */
    protected function readImpl(ReaderInterface $reader)
    {
        throw new PhpBuf\Field\Exception("you mast override function PhpBuf_Field_Abstract#readImpl");
    }

    /**
     * Enter description here...
     *
     * @param PhpBuf\IO\Writer\WriterInterface $writer
     */
    protected function writeImpl(WriterInterface $writer, $value)
    {
        throw new PhpBuf\Field\Exception("you mast override function PhpBuf\Field\Abstract#writeImpl");
    }

    /**
     * Enter description here...
     *
     * @param PhpBuf\IO\Reader\ReaderInterface $reader
     * @return mixed
     */
    protected function readWireTypeData(ReaderInterface $reader)
    {
        //
        // extremely low memory condition [and/or] no class loaded(using autoload): crash calling call_user_func_array
        //
        if (WireType::WIRETYPE_LENGTH_DELIMITED === $this->wireType) {
            return WireType\LenghtDelimited::read($reader);
        }
        return call_user_func_array(array('\PhpBuf\WireType\\' . WireType::getWireTypeNameById($this->wireType), 'read'), array($reader));
    }

    /**
     * Enter description here...
     *
     * @param PhpBuf\IO\Writer\WriterInterface $writer
     * @param mixed $value
     */
    protected function writeWireTypeData(WriterInterface $writer, $value)
    {
        //
        // extremely low memory condition [and/or] no class loaded(using autoload): crash calling call_user_func_array
        //
        if (WireType::WIRETYPE_LENGTH_DELIMITED === $this->wireType) {
            WireType\LenghtDelimited::write($writer, $value);
        } else {
            call_user_func_array(array('\PhpBuf\WireType\\' . \PhpBuf\WireType::getWireTypeNameById($this->wireType), 'write'), array($writer, $value));
        }
    }

    /**
     * Enter description here...
     *
     * @param mixed $value
     * @return boolean
     */
    protected function checkTypeOfValue($value)
    {
        if (Rule::REPEATED === $this->rule && !is_array($value)) {
            return false;
        }
        if (Rule::REPEATED === $this->rule) {
            foreach ($value as $item) {
                if (!$this->checkTypeOfValueImpl($item)) {
                    return false;
                }
            }
            return true;
        }
        return $this->checkTypeOfValueImpl($value);
    }

    /**
     * Enter description here...
     *
     * @param mixed $value
     */
    protected function checkTypeOfValueImpl($value)
    {
        throw new \PhpBuf\Field\Exception("you mast override function PhpBuf_Field_Abstract#checkTypeOfValueImpl");
    }

    /**
     * Enter description here...
     *
     * @param PhpBuf\IO\Writer\WriterInterface $writer
     */
    protected function writeHeader(WriterInterface $writer)
    {
        $value = $this->index << 3;
        $value = $value | $this->wireType;
        Base128::encodeToWriter($writer, $value);
    }

}
