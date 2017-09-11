#!/bin/bash
gitDir=/home/cguo/test/git/
pushDir=/home/cguo/test/download/

declare -A arrP=(['Yue']='ssh://git@code.hillinsight.com/source/yue.git' ['Greenway']='ssh://git@code.hillinsight.com/source/greenway.git')
arrProj=('Yue' 'Greenway' 'Uranium')
echo -n "支持的上线项目: "
for pro in ${arrProj[@]}
do
   echo -n $pro ' '
done
echo ""

read -p "选择要上线的项目: "  project
echo "上线项目: $project"
# 判断是否在支持的项目中todo


read -p "输入要上线的环境[Test(default)/Online]" env
# ${var:=DEFAULT} 如果var没有被声明, 或者其值为空, 那么就以$DEFAULT作为其值
env=${env:="Test"}
echo "上线环境: $env"
if [ $env != 'Test' -a $env != 'Online' ]; then
    echo "请填写Test/Online"
    exit
fi

read -p "输入版本号: "  commit
LENGTH=`echo $commit|wc -L`
if [ $LENGTH -ne 40 ];then
   echo "version_id error "$commit
   exit
fi
echo "版本号: $commit"

# 判断文件夹是否存在，不存在直接创建
dirGitProj=$gitDir$project
dirPushProj=$pushDir$project
if [ ! -d $dirGitProj ]; then
    mkdir -p $dirGitProj
fi
if [ ! -d $dirPushProj ]; then
    mkdir -p $dirPushProj
fi
# git 是否需要初始化
gitExist=$dirGitProj/.git/config
if [ ! -f $gitExist ]; then
    echo "初始化代码仓库"
    fetch=${arrP["Yue"]}
    git clone $fetch $dirGitProj >/dev/null 2>&1
else
    echo "拉取最新代码"
    cd $dirGitProj
    git pull >/dev/null 2>&1
fi

#打包
echo "打包"
cd $dirGitProj
git archive -o $dirPushProj/$project.zip $commit
#解压替换配置文件
cd $dirPushProj
rm -rf $commit
unzip $project.zip -d $commit > /dev/null 2>&1
cd $commit
rm -rf config/config
if [ $env == 'Test' ]; then
    cp -r config/test config/config
elif [ $env == 'Online' ]; then
    cp -r config/online config/config
fi
rm -rf $dirPushProj/$project.zip
cd $dirPushProj
tar czvf $project.tar.gz $commit >/dev/null 2>&1
read -p "是否上传到服务器 [Y/N]?" answer
case $answer in
Y | y)
    if [ $env == 'Test' ]; then
        echo "上传中..."
        scp $project.tar.gz work@ip:/home/work/$project/releases >/dev/null 2>&1
        ssh -t -p 22 work@ip "cd /home/work/$project/releases; rm -rf ${commit}; tar zxvf $project.tar.gz; rm -rf /home/work/$project/current; ln -s /home/work/$project/releases/${commit} /home/work/$project/current; rm -rf $project.tar.gz" > /dev/null 2>&1
        echo "完成"
    elif [ $env == 'Online' ]; then
        echo "permission denied"
    else
       echo "不支持该环境的上线 $1"
    fi
;;
N | n)
      echo "ok,good bye";;
*)
     echo "error choice";;
esac
