#!/bin/bash
function GetPID #User #Name
{

   PsUser=$1
   PsName=$2
   pid=`ps ax -u $PsUser|grep "$PsName"|grep -v grep|grep -v vi|grep -v dbx |grep -v tail|grep -v start|grep -v stop |sed -n 1p |awk '{print $1}'`
   echo $pid
}

checkList[0]="nginx"
checkList[1]="fastcgi"
checkList[2]="mysqld"
checkList[3]="redis-server 1.1.2.3:6379"
checkList[4]="redis-server 1.1.2.3:6479"

time=$(date +"%Y-%m-%d %H:%M:%S")
logTime="system_monitor_"$(date +"%Y%m%d")

for var in "${checkList[@]}";do
   if [ "$var" == 'nginx' ]; then
       PID=`GetPID root nginx`
   elif [ "$var" == 'fastcgi' ]; then
       PID=`GetPID root php-fpm`
   elif [ "$var" == 'mysqld' ]; then
       PID=`GetPID work mysqld`
   elif [ "$var" == 'redis-server 1.1.2.3:6479' ]; then
       PID=`GetPID root "$var"`
   elif [ "$var" == 'redis-server 1.1.2.3:6379' ]; then
       PID=`GetPID root "$var"`
   fi

   if [ "-$PID" == "-" ];then
     echo "${time} | error | 进程${var}不存在" >> /data/wwwlog/sys/$logTime.log
   fi
done

echo "${time} | finish" >> /data/wwwlog/sys/$logTime.log
