<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('xml_merger.php');

$obj = new SimpleXMLElement('<company></company>');

$xmlMerger = new XmlGenerator();

//To create a node with id attribute
$emp1 = $xmlMerger->addXmlNode(
    $obj,
    'employee',
    '',
    ['id' => '108064'],
    'id'
);
$xmlMerger->addXmlNode(
    $emp1,
    'name',
    'Mahesh Singh'
);
$xmlMerger->addXmlNode(
    $emp1,
    'mobile',
    '01234567890'
);

// Replace previous node
// $xmlMerger->addXmlNode(
//     $obj,
//     'employee',
//     'Mahesh Singh Rajawat',
//     ['id' => '108064'],
//     'id'
// );

//again create a new node with attributes
$emp2 = $xmlMerger->addXmlNode(
    $obj,
    'employee',
    '',
    ['id' => '108065'],
    'id'
);

$xmlMerger->addXmlNode(
    $emp2,
    'name',
    'John Doe'
);
$xmlMerger->addXmlNode(
    $emp2,
    'mobile',
    '01234567890'
);

echo $xmlMerger->formatXml($obj->asXml());