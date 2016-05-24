https://bugs.php.net/bug.php?id=66528

<?php
$dbh = new PDO('mysql:dbname=test;host=127.0.0.1;charset=UTF8', 'testuser', '');
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// prepare table for test
$dbh->query('DROP TABLE IF EXISTS importantdata');
$dbh->query('create table test.importantdata (a int) engine=innodb');

try {

    $dbh->beginTransaction();
    $dbh->query('insert into importantdata (a) VALUES (1), (2)');

    sleep(20); // shut down mysql-server
    
    $dbh->commit();

} catch (PDOException $e) {
    exit($e->getMessage());
}
print_r($dbh->errorInfo());
echo 'I should never get here';
?>

Expected result:
----------------
"SQLSTATE[HY000]: General error: 2006 MySQL server has gone away" 
[... Script execution stopped]


Actual result:
--------------
Warning: PDO::commit(): MySQL server has gone away in /tmp/test.php on line 16

Warning: PDO::commit(): Error reading result set's header in /tmp/test.php on line 16
Array
(
    [0] => 00000
    [1] => 
    [2] => 
)

I should never get here
[... Script execution proceeds]


如果sleep后面还有sql执行，那会得到Expected result。后面没有sql的话不会抛出错误。
