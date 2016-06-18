#awk#

#示例#

1, 长度超过半角30个字符的行

awk 'length > 30' inputfile

2, 字段数在5个以上10个以下的行

awk 'NF >= 5 && NF <= 10' inputfile

3, 只表示最开始的5行

awk 'NR <= 5' inputfile

4, 只表示偶数行

awk 'NR % 2 == 0' inputfile

5, 表示第一个字段的值比100小的行

awk '$1 < 100' inputfile

6, 包含“问题”和“答案”的行

awk '/问题|答案/' inputfile

7, 以'#'开始的行

awk '/^#/' inputfile

8, 删除空白行

awk 'length > 0' inputfile
awk 'NF != 0' inputfile



awk -F'[ab]' '{print $x}' inputfile
= awk -F 'a|b' '{print $x}' inputfile

#abc#:123#

awk -F'[#:]' 则分为 # abc #: 123 # '{print $2, $4} 分别得到abc 123 分隔符是不可见的




