#csv#

##1，csv是什么？ ##

逗号分隔值（Comma-Separated Values，CSV，有时也称为字符分隔值，因为分隔字符也可以不是逗号）， 其文件以纯文本形式存储表格数据（数字和文本）。
纯文本意味着该文件是一个字符序列，不含必须象二进制数字那样被解读的数据。CSV文件由任意数目的记录组成，记录间以某种换行符分隔；每条记录由字段组成，字段间的分隔符是其它字符或字符串，最常见的是逗号或制表符。通常，所有记录都有完全相同的字段序列。

##2，什么时候用？##

CSV是一种通用的、相对简单的文件格式，被用户、商业和科学广泛应用。最广泛的应用是在程序之间转移表格数据，而这些程序本身是在不兼容的格式上进行操作的（往往是私有的和/或无规范的格式）。因为大量程序都支持某种CSV变体，至少是作为一种可选择的输入/输出格式。

##3, php处理csv文件 ##

##4, python处理csv文件 ##

##5, excel展示csv文件##

http://jingyan.baidu.com/article/76a7e409d2a25afc3b6e150d.html

1, cat filename.csv | pbcopy
2, 打开excel Contral+v
3, 选中一列，点击数据->分列
4, 进入文本分列向导，选中分隔符号（D）

问题：

15458,100456050,2016,戏剧和你,都是风景-孟京辉导演《两只狗的生活意见》,苏州市,话剧舞台剧,2016-08-18 19:30,2016-08-19 19:30,2016-08-20 19:30

这种文本中就带逗号的分导致分隔失败

<pre><code>
require "db.php";

$sql = "select * from table_name order by product_id desc limit 10";
$result = DB::instance('db_name')->read($sql);
$arrCSV = array();
foreach ($result as $row) {
    $arrCSV[] = array(
        $row['row_1'],
        $row['row_2'],
        $row['row_3'],
    );
}
$fp = fopen("./file.csv", 'w');
foreach ($arrCSV as $fields) {
    fputcsv($fp, $fields);
}
fclose($fp);
</code></pre>

#csv整型在excel展示时丢失精度#

csv超过15位的整数在excel展示时会有丢精度，需要在字段前面加上'='或在后面加'\t'来解决
