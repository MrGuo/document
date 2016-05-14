#innodb_lock#

InnoDB存储引擎实现了两种标准的行级锁

* 共享锁，允许事务读取一行数据
* 排他锁，允许事务删除或更新一行记录

如果一个事务T1已经获得了行r的共享锁，那么另外的事务T2可以立即获得行的共享锁，因为读取并没有改变行r的数据。这种情况叫做锁兼容（Lock Compatible）
但若有其他的事务T3想要获得行r的排他锁，则其必须要等到T1, T2释放行r上的共享锁。这种情况叫做锁不兼容。

T1

1, begin
2, select * from t_test_1 where id=1 lock in share mode;
3, commit


T2 

1, begin
2, update t_test_1 set name='xxx' where id=1
3, commit


在T1执行到2的时候，T2.2操作会等待T1.2执行完毕才能执行

show engine innodb status\G;

> update t_test_1 set name='guochao' where id=1

> ------- TRX HAS BEEN WAITING 8 SEC FOR THIS LOCK TO BE GRANTED:

> RECORD LOCKS space id 23 page no 3 n bits 72 index PRIMARY of table `test`.`t_test_1` trx id 274700 lock_mode X locks rec but not gap waiting

> Record lock, heap no 4 PHYSICAL RECORD: n_fields 4; compact format; info bits 0

>  0: len 4; hex 80000001; asc     ;;

>  1: len 6; hex 00000004310a; asc     1 ;;

>  2: len 7; hex 280000033824c7; asc (   8$ ;;

>  3: len 7; hex 67756f6368616f; asc guochao;;

同样，如果先执行T2，那执行到T2.2的时候，再执行T1.2时也要等待T2.3执行完毕


如果SELECT ... LOCK IN SHARE MODE命令本身是某个事务的一部分，在它开始执行之后，在这个事务结束之前，数据表里与其结果记录有关的所有数据都将被锁定，其他客户将只能读取，不能修改或删除那些数据。共享锁可以确保在事务过程读取的数据记录不会是其他客户正在修改或删除的。


关键字SELECT ... FOR UPDATE这个关键字将给数据表里与这条SELECT命令的结果记录有关的所有数据加上一把排他锁。排它锁不禁止其他客户使用曾通的SELECT命令来读取被锁定的数据记录，但其他客户对那些记录进行的修改和删除操作以及使用SELECT ... LOCK IN SHARE MODE命令进行的读取操作都将被阻断。

Insert, Update和Delete都会在开始执行后将要修改/删除的数据记录加上一把排它锁直到本个事务结束。

在默认情况下，InnoDB数据表驱动程序在遇到带有范围条件表达式（比如where id > 100或where id between 100 and 200）的SELECT ... LOCK IN SHARE MODE, SELECT ... FOR UPDATE, UPDATE 或INSERT命令还会多给他们加上一把防插入锁。加上这把锁的效果是不仅符合条件的现有数据会被锁定，连符合给定条件但当前不存在的数据也会被锁定，而这意味着其他事务将无法在有关的数据表里插入符合给定条件的数据记录。比如说在某个事务里执行了SELECT ... WHERE id > 100 FOR UPDATE命令，那么在事务结束之前，其他用户将无法插入id > 100的新记录。

InnoDB数据表驱动程序能够自动识别死锁（两个进程或多个进程互相阻塞，彼此都在等待对方结束，结果是谁都无法继续执行）条件并能加以消除；触发死锁的那个进程（后来者）将收到一条出错消息，该进程尚未提交的所有SQL命令按ROLLBACK方式撤销，另一个进程（先来者）将继续执行。

INNODB 1.0版本之前，只能通过命令SHOW ENGINE INNODB STATUS等来查看当前数据库中锁的请求，然后再判断事务锁的情况。从1.0开始，在INFORMATION_SCHEMA架构下添加了表INNODB_TRX, INNODB_LOCKS，INNODB_LOCK_WAITS。通过这三张表，用户可以更简单的监控当前事务表并分析可能存在的锁的问题。
