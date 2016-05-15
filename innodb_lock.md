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


Insert, Update和Delete都会在开始执行后将要修改/删除的数据记录加上一把排它锁直到本个事务结束。
在默认情况下，InnoDB数据表驱动程序在遇到带有范围条件表达式（比如where id > 100或where id between 100 and 200）的SELECT ... LOCK IN SHARE MODE, SELECT ... FOR UPDATE, UPDATE 或INSERT命令还会多给他们加上一把防插入锁。加上这把锁的效果是不仅符合条件的现有数据会被锁定，连符合给定条件但当前不存在的数据也会被锁定，而这意味着其他事务将无法在有关的数据表里插入符合给定条件的数据记录。比如说在某个事务里执行了SELECT ... WHERE id > 100 FOR UPDATE命令，那么在事务结束之前，其他用户将无法插入id > 100的新记录。
InnoDB数据表驱动程序能够自动识别死锁（两个进程或多个进程互相阻塞，彼此都在等待对方结束，结果是谁都无法继续执行）条件并能加以消除；触发死锁的那个进程（后来者）将收到一条出错消息，该进程尚未提交的所有SQL命令按ROLLBACK方式撤销，另一个进程（先来者）将继续执行。
INNODB 1.0版本之前，只能通过命令SHOW ENGINE INNODB STATUS等来查看当前数据库中锁的请求，然后再判断事务锁的情况。从1.0开始，在INFORMATION_SCHEMA架构下添加了表INNODB_TRX, INNODB_LOCKS，INNODB_LOCK_WAITS。通过这三张表，用户可以更简单的监控当前事务表并分析可能存在的锁的问题。

一致性非锁定读是指InnoDB存储引擎通过多版本控制的方法来读取当前执行时间数据库中行的数据。如果读取的行正在执行update或delete操作，这时读取操作不会去等待行上锁的释放。相关的，InnoDB存储引擎会去读取行的一个快照数据。非锁定读的方式极大的提高了数据库的并发性。在InnoDB存储引擎的默认设置下，读取不会占用和等待表上的锁。但是在不同的事务隔离级别下，读取的方式不同，并不是在每个事务隔离级别下都是采用非锁定的一致性读，此外，即使都是使用非锁定的一致性读，但是对于快照数据的定义也不相同。
快照数据其实就是当前行数据之前的历史版本，每行记录可能有多个版本。一个行记录可能有不止一个快照数据，一般称这种技术为行多版本技术。由此带来的并发控制，称之为多版本并发控制技术(Multi Version Concurrency Control, MVCC)。
在事务隔离级别READ COMMITTED和REPEATABLE READ(InnoDB默认事务隔离级别)下，InnoDB存储引擎使用非锁定一致性读。然而对于快照数据的定义却不相同。在READ COMMITED事务隔离模式下，对于快照数据，非一致性读总是读取被锁定行的最新一份快照数据。而在REPEATABLE READ事务隔离模式下，对于快照数据，非一致性读总是读取事务开始时的行数据版本。


select @@tx_isolation;

REPEATABLE-READ

对于REPEATABLE_READ的事务隔离级别，总是读取事务A开始时的行数据，因此在其他事务B中即使已经commit，在A中再读取修改的行数据也不会改变。而READ COMMITED总是读取行的最新版本。


在默认配置下，即事务的隔离级别为REPEATABLE READ模式下，InnoDB存储引擎的SELECT操作使用一致性非锁定读。但是在某些情况下，用户需要显式的对数据库读取操作进行加锁已保证数据逻辑的一致性。而这要求数据库支持加锁语句

* SELECT ... FOR UPDATE
* SELECT ... LOCK IN SHARE MODE

SELECT ... FOR UPDATE对读取的行记录加一个X锁，其他事务不能对已锁定的行加上任何锁。SELECT ... LOCK IN SHARE MODE对读取的行记录加一个S锁，其他事务可以向被锁定的行加S锁，但是如果加X锁，则会被阻塞。


