## C语言字符串

###1,常量字符串

```
char *pmessage = "Hello world\n";
```
在程序执行的时候，系统会将它们放在常量区。pmessage是一个指针，其初值指向一个字符串常量，之后它可以被修改为指向其他地址，但如果试图修改字符串的内容，结果是没有定义的。
常量区是一直存在的，只读不可更改的数据区域，并且一个字符串只会有一份

```
char *pmessage = "Hello world\n";
char *lmessage = "Hello world\n";
char amessage[] = "Hello world\n";

printf("%p\n%p\n%p\n", pmessage, lmessage, amessage);

输出：
0x105868f8e
0x105868f8e
0x7fff5a397a5b
```

```
char amessage[] = "Hello world\n";
```
amessage是一个仅仅足以存放初始化字符串及空字符'\0'的一维数组，数组中的单个字符可以修改，但amessage始终指向同一个存储位置。执行到这一行，内存中就有两个Hello world的字符串，一个是常量区域的，一个是根据前者复制了一份的，这句代码的意思就是复制一个常量区域的字符串，将复制后的字符串的首字母的地址赋值给amessage。
也就是说，最后amessage所指向的内存区域，已经不是常量里的"Hello World\n"了，这里为什么要复制一份呢？原因是常量是不允许更改的，而数组一般都意味需要修改，所以就复制了一份数据，放在非常量区域，就可以更改了。

GCC会把字符串常量放到只读的.rodata。.rodata data和txt一样是操作系统保护只读的，实际中大部分操作系统会按照内存越权访问处理。建议用char \*s ="hello" 时用 const char \*s = "hello"代替


