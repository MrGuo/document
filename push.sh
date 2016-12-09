#!/bin/bash

function init()
{
    cd "xxxxxx";
}

function download()
{
    echo "download start...\n";
    curl -o ret.tar.gz xxxxxxx
    echo "download success\n";
}

function proc()
{
    tar -zxvf ret.tar.gz
    IN=`ls | egrep 'xxxxxx.'`;
    VERSION_ID=${IN##*-}
    if [ ! -x "$VERSION_ID" ]; then
       mv $IN $VERSION_ID
    else
       echo "$VERSION_ID 已经存在"
    fi

    if [ $1 == 'online' ]; then
       ENV='index_online'
    elif [ $1 == 'test' ]; then
       ENV='index_prod'
    else
       ENV='index_dev'
    fi
    FRONT_INDEX=${VERSION_ID}"/frontend/web/index.php"
    FRONT_ENV_INDEX=${VERSION_ID}"/frontend/web/"${ENV}".php"
    cp $FRONT_ENV_INDEX $FRONT_INDEX

    BACKEND_INDEX=${VERSION_ID}"/backend/web/index.php"
    BACKEND_ENV_INDEX=${VERSION_ID}"/backend/web/"${ENV}".php"
    cp $BACKEND_ENV_INDEX $BACKEND_INDEX

    scp -r $VERSION_ID www@ip:/home/www/code/xxxx

    ssh -t -p port www@ip "rm -rf /home/www/code/current; ln -s /home/www/code/${VERSION_ID} /home/www/code/current"

    rm -rf $IN
    rm -rf ret.tar.gz
}

# 环境支持 online test dev

init
download
proc online
