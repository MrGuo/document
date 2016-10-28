#!/usr/bin/env python
# coding=utf-8

from fabric.api import *
from fabric.colors import *
from fabric.context_managers import *
import time
import os

env.hosts = ['ip:port']
env.user = 'root'
env.password = 'pass'
env.env = 'prod'

env.project_tar_source = '/Users/guochao/releases/' # 开发机项目压缩包存储目录
env.project_pack_name  = 'retailerp' # 压缩包名前缀，文件名为release.tar.gz

gitlab_url = 'http://git.hillinsight.com/hillinsight-rd/retailerp/repository/archive.tar.gz?ref=dev_start&private_token=pcbCx62vyFz447bKPYRz'

env.project_front_root = 'server/frontend/web/'
env.project_back_root = 'server/backend/web/'
env.project_script_root = 'server/console/script/'

env.deploy_project_root = '/home/work/retailerp/' # 生产环境项目主目录
env.deploy_release_dir  = 'releases' # 项目发布目录,位于主目录下
env.deploy_current_dir = 'current' # 对外服务的当前版本软链接
env.deploy_version = time.strftime("%Y%m%d") + "v1" # 版本号
env.deploy_version_tmp = time.strftime("%Y%m%d") + "t1" # 临时版本号



@task
def code_download(): # 从gitlab下载源码
    print yellow("Download code from gitlab...")
    with lcd(env.project_tar_source):
        local("curl -o %s.tar.gz '%s'" % (env.project_pack_name, gitlab_url))
    print green("Download code success")


@runs_once
def input_versionid():
    return prompt("please input project rollback version ID:", default="")

@task
def put_package(): # 上传任务函数
    print yellow("Start put package...")
    with settings(warn_only = True):
        with cd(env.deploy_project_root + env.deploy_release_dir):
            run("rm -rf %s" % (env.deploy_version_tmp))
            run("mkdir %s" % (env.deploy_version_tmp))
    deploy_full_path = env.deploy_project_root + env.deploy_release_dir + "/" + env.deploy_version_tmp
    with settings(warn_only=True):
        result = put(env.project_tar_source + env.project_pack_name + ".tar.gz", deploy_full_path)
    if result.failed and not ("put file failed, Continue[Y/N]?"):
        abort("Aborting file put task")
    with cd(deploy_full_path):
        run("tar -zxvf %s.tar.gz" % (env.project_pack_name))
        run("rm -rf %s.tar.gz" % (env.project_pack_name))
        run("mv `ls | egrep 'retailerp-dev_start.'`/* ./ ; rm -rf `ls | egrep 'retailerp-dev_start.'`")
    print green("put & untar package success!")

@task
def make_symlink(): #为当前目录做软链
    print yellow("update current symlink")
    deploy_full_path_tmp = env.deploy_project_root + env.deploy_release_dir + "/" + env.deploy_version_tmp
    deploy_full_path = env.deploy_project_root + env.deploy_release_dir + "/" + env.deploy_version
    with settings(warn_only = True):
        run("rm -rf %s" % deploy_full_path)
        run("mv %s %s" % (deploy_full_path_tmp, deploy_full_path))

        fontroot = deploy_full_path + '/' + env.project_front_root + 'index_' + env.env + '.php'
        fontroot_index = deploy_full_path + '/' + env.project_front_root + 'index.php'
        backroot = deploy_full_path + '/'+ env.project_back_root + 'index_' + env.env + '.php'
        backroot_index = deploy_full_path + '/' + env.project_back_root + 'index.php'
        scriptroot = deploy_full_path + '/'+ env.project_script_root + 'script_' + env.env + '.php'
        scriptroot_index = deploy_full_path + '/' + env.project_script_root + 'script.php'

        run ("rm %s" % fontroot_index)
        run ("rm %s" % backroot_index)
        run ("rm %s" % scriptroot_index)
        run ("ln -s %s %s" % (fontroot, fontroot_index)) # index 软链
        run ("ln -s %s %s" % (backroot, backroot_index)) # index 软链
        run ("ln -s %s %s" % (scriptroot, scriptroot_index)) # index 软链

        # current
        run("rm %s" % (env.deploy_project_root + env.deploy_current_dir))
        run("ln -s %s %s" % (deploy_full_path, env.deploy_project_root + env.deploy_current_dir))

    print green("make symlink success!")

@task
def rollback():
    print yellow("rollback project version")
    versionid = input_versionid()
    if versionid == '':
        abort("Project version ID error, abort")

    env.deploy_full_path = env.deploy_project_root + env.deploy_release_dir + "/" + versionid
    run("rm -f %s" % env.deploy_project_root + env.deploy_current_dir)
    run("ln -s %s %s" % (env.deploy_full_path, env.deploy_project_root + env.deploy_current_dir))
    print green("rollback success!")

@task
def go():
    code_download()
    put_package()
    make_symlink()

