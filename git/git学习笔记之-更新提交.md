版本回退

git log --pretty=oneline

ad148b5c19f6b55bcb86a826b219665e01b052be add func 2 in update
c5a746163d83c0a6ea97454bc05ddaf59a6cae60 func 1 in update
00a084975034914581ff66e021e04469ca95a728 Init update

commit id 不是递增的数字，而是一个SHA1计算出来的一个非常大的数字，用十六进制表示。因为Git是分布式的版本控制系统，如果很多人在同一个版本库里工作，用1,2,3作为版本号就会冲突。

如果我们想把Update.class.php回退到上一个版本，也就是func 1 in update那个版本。可以使用git reset 命令

在Git中，用HEAD表示当前版本，也就是最新提交的ad148b5c19f6b55bcb86a826b219665e01b052be，上一个版本就是HEAD^，上上一个版本就是HEAD^^，向上n个版本写成HEAD~n

git reset —hard HEAD^

输出：HEAD is now at c5a7461 func 1 in update

这个时候再用git log —pretty=oneline 发现add func 2 in update 已经没有了。

这个时候如果还想要回到修改前的版本呢？

git reset --hard ad148b5c19f6b55b

输出

HEAD is now at ad148b5 add func 2 in update

版本号没有必要写全，前几位就可以了，Git会自动的去找。

这个commit id如果忘记了怎么办？使用命令

git reflog

撤销修改

现在修改了一个文件，比比替换了文件中一个单词的拼写（有n个），但是过了一会发现不是很合适，想要再撤销修改。这个时候可以使用命令：

git checkout — file

上面命令可以丢弃工作区修改，或暂存区的修改。

总之，就是让这个文件回到最近一次git commit或git add时的状态。

git checkout —file中的 —很重要，没有这个—，就变成了“切换到另一个分支”的命令。

修如你已经svn add到了暂存区，可以使用如下命令把暂存区的修改撤销掉，重新放回工作区：

git reset HEAD filename

场景：

1，如果你改乱了某个文件的内容，想直接丢弃工作区的修改，用命令git checkout — file

2，当你不但改乱了工作区某个文件的内容，还添加到了暂存区时，想丢弃修改，分两步，第一步用git reset HEAD file，就回到了场景1，每二步按场景1操作。

3，已经提交了不合适的修改到版本库时，想要撤销本次提交，参考上面版本回退一节，不过前提是没有推送到远程库。

删除文件：

一般情况上，你通常直接在文件管理中把没有用的文件删了

rm test.txt

这个时候，Git知道你删除了文件，因此，工作区和版本库就不一致了，git status命令就会立刻告诉你哪些文件被删除了：

git status

Changes not staged for commit:
  (use "git add/rm <file>..." to update what will be committed)
  (use "git checkout -- <file>..." to discard changes in working directory)

    deleted:    User.class.php

已经给出提示，要么使用git rm 来从版本库中删除。

另一种情况是删错了，可以使用

git checkout — file来把误删的文件恢复到最新版本。
