<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h2>PHP Error Testing</h2>";

echo $undefined_variable;

echo UNDEFINED_CONSTANT;

$result = 10 / 0;

$array = ['a' => 1];
echo $array['nonexistent_key'];

$str = null;
echo strlen($str);

class TestClass {}
$obj = new TestClass();
$obj->undefinedProperty;

function testTypeHint(int $value): string {
    return $value;
}
testTypeHint("not an int");

$arr = [];
echo $arr[0][1][2];

$file = fopen('/nonexistent/path/file.txt', 'r');

include 'nonexistent_file.php';

echo "<h3>phpinfo()</h3>";
phpinfo();

echo "<h2>Security Vulnerability Testing</h2>";

$username = $_GET['username'] ?? 'admin';
$password = $_GET['password'] ?? 'password123';

$db = new PDO('sqlite:timekeeping.db');
$query = "SELECT * FROM entries WHERE project = '" . $_GET['project'] . "'";
$result = $db->query($query);

echo "<div>" . $_GET['input'] . "</div>";

$cmd = $_GET['cmd'] ?? 'ls';
$output = shell_exec($cmd);
echo "<pre>$output</pre>";

$page = $_GET['page'] ?? 'home';
include($page . '.php');

$filename = $_GET['file'] ?? 'test.txt';
$content = file_get_contents($filename);
echo $content;

$api_key = "sk-1234567890abcdef";
$db_password = "super_secret_password_123";
echo "Debug: API Key = $api_key, DB Pass = $db_password";

session_start();
$_SESSION['user_id'] = 1;
$_SESSION['is_admin'] = true;

eval($_GET['code'] ?? 'echo "test";');

$file = $_FILES['upload'] ?? null;
if ($file) {
    move_uploaded_file($file['tmp_name'], 'uploads/' . $file['name']);
}

$redirect = $_GET['url'] ?? 'index.html';
header("Location: $redirect");
