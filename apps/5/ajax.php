<?php
$users = [
    [
        'id' => 1,
        'name' => 'Frank',
        'email' => 'frank2017@gmail.com'
    ],
    [
        'id' => 2,
        'name' => 'John',
        'email' => 'jg@outlook.com'
    ],
    [
        'id' => 3,
        'name' => 'Marry',
        'email' => 'marryS@gmail.com'
    ]
];
if ( !validateData() )
{
    logData([$_REQUEST, 'result' => 'failed-validate']);
    echo json_encode(['result' => 'failed-validate']);
    exit;
}
if ( !checkUser() )
{
    logData([$_REQUEST, 'result' => 'duplicate-user']);
    echo json_encode(['result' => 'duplicate-user']);
    exit;
}
logData([$_REQUEST, 'result' => 'success']);
echo json_encode(['result' => 'success']);
function validateData() : bool
{
    if ( !str_contains($_REQUEST['email'], '@')
         ||
         $_REQUEST['password'] !== $_REQUEST['repeat-password']
    )
    {
        return false;
    }
    return true;
}
function checkUser() : bool
{
    global $users;
    if ( in_array($_REQUEST['email'], array_column($users, 'email')) )
    {
        return false;
    }
    return true;
}
function logData(array $data) : void
{
    file_put_contents(__DIR__.'/logs.txt',var_export($data,1)."\r\n".date('H:i:s')."\r\n\r\n====================\r\n", FILE_APPEND);
}