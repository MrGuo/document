#xargs#

删除某些以abcdefg开头的key

redis-cli keys "abcdefg*" |xargs redis-cli DEL

redis-cli keys "yongle:product:long:desc*" | head -n 2
输出
yongle:product:long:desc:16
yongle:product:long:desc:18

redis-cli del yongle:product:long:desc:16
输出
integer (1)

https://zh.wikipedia.org/wiki/Xargs

find . -name "*.foo" | xargs grep bar

相当于

grep bar `find . -name "*.foo"`
