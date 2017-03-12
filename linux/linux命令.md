(1) du 查看使用空间

常用：

du -sh library
du -h file_name
du -h file_name_1 file_name_2
du -ch file_name_1 file_name_2
显示目录下文件所占空间
du -s *
显示前十个占用空间最大的文件或目录
du -s * | sort -nr | head


-s 仅显示总计，只列出最后加总的值
-h 以K,M,G为单位，提高可读性
-c 除了显示个别目录或文件的大小外，同时也显示所有目录或文件的总和


(2) xargs

见xargs.md
