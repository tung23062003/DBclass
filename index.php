<?php
require('database.class.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$config = [
    'host' => 'localhost',
    'user' => 'root',
    'pass' => '',
    'name' => 'dbclass'
];

$db = new database($config);


//    INSERT

/* 
$db->table('users')->insert([
    'username' => 'zxczxc',
    'name' => 'ahihi'
]);
 */


//    SELECT

// $results = $db->table('users')->join('product')->where(
//     [
//         'id' => ['>', 1]
//     ]
// )->LIMIT(5)->OFFSET(0)->get(['*']);

// foreach($results as $result){
//     echo $result->id . ' | ';
//     echo $result->username . ' | ';
//     echo $result->name . '<br>';
// }




//     EDIT
/* 
$db->table('users')->where(
    [
        'id' => ['<', 6],
        'username' => 'tung0603'
    ])->update(
    [
        'username' => 'tung2003',
        'name' => 'tung xau zai'
    ]
);

 */


//     DELETE
/* 
$db->where([
    'id' => ['>=', 2],
    'username' => 'tung03'
])->table('users')->delete();

 */

 