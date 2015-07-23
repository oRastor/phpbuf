<?php

/**
 * Use tests in folder tests
 */
require_once 'vendor/autoload.php';

require_once("./tests/Example.php");
require_once("./tests/ExampleRepeat.php");
require_once("./tests/Example/Test1.php");
require_once("./tests/Example/Test3.php");

echo "Test values less than 128 (encode/decode)\n";
$result = PhpBuf\Base128::decodeFromReader(new PhpBuf\IO\Reader(PhpBuf\Base128::encode(120)));
echo "120 == " . $result . "\n\n";
echo "Test values more than 128 (only decode)\n";
$result = PhpBuf\Base128::decodeFromReader(new PhpBuf\IO\Reader(pack("n", 44034)));
echo "300 == " . $result . "\n\n";
echo "Test values more than 128 (encode/decode)\n";
$encode = PhpBuf\Base128::encode(300);
$result = PhpBuf\Base128::decodeFromReader(new PhpBuf\IO\Reader($encode));
echo "encoded: $encode\n\n";
echo "300 == " . $result . "\n\n";
echo "Test values more than 128 (encode/decode)\n";
$encode = /* pack("n", 8891723); */PhpBuf\Base128::encode(123456789);
$result = PhpBuf\Base128::decodeFromReader(new PhpBuf\IO\Reader($encode));
echo "encoded: $encode\n\n";
echo "123456789 == " . $result . "\n\n";
echo "Test ZigZag class\n";
echo "value: 0\n";
$encode = PhpBuf\ZigZag::encode(0);
echo "encoded: " . $encode . "\n";
echo "decoded: " . PhpBuf\ZigZag::decode($encode) . "\n\n";

echo "value: 1\n";
$encode = PhpBuf\ZigZag::encode(1);
echo "encoded: " . $encode . "\n";
echo "decoded: " . PhpBuf\ZigZag::decode($encode) . "\n\n";

echo "value: -1\n";
$encode = PhpBuf\ZigZag::encode(-1);
echo "encoded: " . $encode . "\n";
echo "decoded: " . PhpBuf\ZigZag::decode($encode) . "\n\n";

echo "value: 123456789\n";
$encode = PhpBuf\ZigZag::encode(123456789);
echo "encoded: " . $encode . "\n";
echo "decoded: " . PhpBuf\ZigZag::decode($encode) . "\n\n";

echo "value: -123456789\n";
$encode = PhpBuf\ZigZag::encode(-123456789);
echo "encoded: " . $encode . "\n";
echo "decoded: " . PhpBuf\ZigZag::decode($encode) . "\n\n";

echo "Test Reader & Writer\n";

$writer = new PhpBuf\IO\Writer();

$writer->writeBytes("test1");
echo "lenght: " . $writer->getLength() . "\n";
echo "test1 = " . $writer->getData() . "\n";
$writer->writeBytes("test2");
echo "lenght: " . $writer->getLength() . "\n";
echo "test1test2 = " . $writer->getData() . "\n";
$writer->redo();
echo "lenght: " . $writer->getLength() . "\n";
echo "test1 = " . $writer->getData() . "\n";
$writer->writeByte("!");
echo "lenght: " . $writer->getLength() . "\n";
echo "test1! = " . $writer->getData() . "\n";
$writer->redo();
echo "lenght: " . $writer->getLength() . "\n";
echo "test1 = " . $writer->getData() . "\n";
$writer->writeBytes(" StringForReaders");
echo "lenght: " . $writer->getLength() . "\n";
echo "test1 StringForReaders = " . $writer->getData() . "\n";

$reader = PhpBuf\IO\Reader::createFromWriter($writer);
echo "t = " . $reader->getByte() . "\n";
echo "position: " . $reader->getPosition() . "\n";
echo "e = " . $reader->getByte() . "\n";
echo "position: " . $reader->getPosition() . "\n";
echo "st1 = " . $reader->getBytes(3) . "\n";
echo "position: " . $reader->getPosition() . "\n";
$reader->setPosition(12);
echo "For = " . $reader->getBytes(3) . "\n";
echo "position: " . $reader->getPosition() . "\n";
$reader->redo();
echo "For = " . $reader->getBytes(3) . "\n";
echo "position: " . $reader->getPosition() . "\n";

echo "Test Messages\n";
$message = new PhpBuf_Message_Example();

$writer = new PhpBuf\IO\Writer();
$message->id = 150;
$message->balance = -12345;
$message->isAdmin = true;
$message->status = "deleted";
$message->name = "Andrey Lepeshkin";
$message->write($writer);

echo $writer->getData() . "\n";
foreach (str_split($writer->getData()) as $byte) {
    echo ord($byte) . "\n";
}
$reader = PhpBuf\IO\Reader::createFromWriter($writer);
$message = new PhpBuf_Message_Example();
$message->read($reader);
echo "id: " . $message->id . "\n";
echo "balance: " . $message->balance . "\n";
if ($message->isAdmin === true) {
    echo "isAdmin: true\n";
}
echo "status: " . $message->status . "\n";
echo "name: " . $message->name . "\n";


$messagesArray = array();
$main = new PhpBuf_Message_ExampleRepeat();
$writer = new PhpBuf\IO\Writer();
for ($i = 0; $i < 5; $i++) {
    $nested = new PhpBuf_Message_Example();
    $nested->id = $i;
    $nested->balance = -12345 + ($i * 10);
    $nested->isAdmin = false;
    $nested->status = "deleted";
    $nested->name = "name for $i";
    $nested->bytes = "Some bytes for $i";
    $messagesArray[] = $nested;
}
$main->messages = $messagesArray;
$main->write($writer);

$reader = PhpBuf\IO\Reader::createFromWriter($writer);
$main = new PhpBuf_Message_ExampleRepeat();
$main->read($reader);

print_r($main);
