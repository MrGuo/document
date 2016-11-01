#!/usr/bin/env python
# coding=utf-8

from fabric.api import *
from fabric.colors import *
from fabric.contrib.console import confirm
import time

env.hosts = ['ip:port']
env.user = 'aaa'
env.password = 'bb'

env.project_tar_source = '/Users/guochao/releases/' # 开发机项目压缩包存储目录
env.project_pack_name  = 'h5front' # 压缩包名前缀，文件名为h5front.tar.gz

gitlab_url = 'httrTqX_czLwBz'

env.deploy_version = time.strftime("%Y%m%d") + "v1" # 版本号
env.deploy_version_tmp = time.strftime("%Y%m%d") + "t1" # 临时版本号

env.version = 'v0.1.10'

#TODO:
environments = ['development', 'production', 'test']

@task
def code_download():
    print yellow("Download code from gitlab...")
    with lcd(env.project_tar_source):
        local("curl -o %s.tar.gz '%s'" % (env.project_pack_name, gitlab_url))
    print green("Download code success")


@task
def put_package(): # 上传任务函数
    print yellow("Start put package...")
    with settings(warn_only = True):
        with cd('/home/front/releases/backup'):
            run("rm -rf %s" % (env.deploy_version_tmp))
            run("mkdir %s" % (env.deploy_version_tmp))
    deploy_full_path = "/home/front/releases/backup/" + env.deploy_version_tmp
    with settings(warn_only=True):
        result = put(env.project_tar_source + env.project_pack_name + ".tar.gz", deploy_full_path)
    if result.failed and not ("put file failed, Continue[Y/N]?"):
        abort("Aborting file put task")
    with cd(deploy_full_path):
        run("unzip %s.tar.gz" % (env.project_pack_name))
        run("rm -rf %s.tar.gz" % (env.project_pack_name))
        # 这里有风险todo
        run("mv `ls | egrep 'pet-wexin'`/* `ls | egrep 'pet-wexin'`/.[^.]* ./ ; rm -rf `ls | egrep 'pet-wexin'`")
    print green("put & untar package success!")

@task
def make_symlink(): #为当前目录做软链
    print yellow("update current symlink")
    deploy_full_path_tmp = '/home/front/releases/backup/' + env.deploy_version_tmp
    deploy_full_path = "/home/front/releases/backup/" + env.deploy_version
    with settings(warn_only = True):
        with cd (deploy_full_path_tmp):
	    run('bower install')
	    run('ln -s /home/front/releases/thirdparty/node_modules .')
	    run('gulp build')
        # 重命名新目录
        run ("rm -rf %s" % deploy_full_path)
        run("mv %s %s" % (deploy_full_path_tmp, deploy_full_path))
        with cd ('/home/front/releases/'):
            run("rm pet")
            run("ln -s %s %s" % (deploy_full_path, 'pet'))
	run('pm2 restart www')
    print green("make symlink success!")

@task
def go():
    code_download()
    put_package()
    make_symlink()
