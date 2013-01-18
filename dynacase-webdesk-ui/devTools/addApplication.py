#!/usr/bin/env python
# -*- coding: UTF-8 -*-

import sys
import os
import shutil
import fileinput
from tempfile import mkdtemp
from string import Template
from os.path import dirname

from utils import copytree

if sys.version_info < (2, 7):
    raise "must use python 2.7 or greater"
import argparse

def parseOptions():
    argParser = argparse.ArgumentParser(
        description='add a new application in current module'
    )
    argParser.add_argument('appName',
        help = 'application name')
    argParser.add_argument('-c', '--childof',
        help = 'parent Application',
        dest = 'childOf',
        default = '')
    argParser.add_argument('--templateDir',
        help = 'templates directory',
        dest = 'templateDir',
        default = None)
    argParser.add_argument('--targetDir',
        help = 'target directory, where generated files will be placed',
        dest = 'targetDir',
        default = None)
    args = argParser.parse_args()
    return args

def addApplication(appName, childOf='', appShortName='', templateDir=None, targetDir=None):

    if targetDir is None:
        targetDir = os.path.join(dirname(dirname(__file__)), 'Apps')

    if templateDir is None:
        templateDir = os.path.join(dirname(__file__), 'templates')

    toMoveFiles = [
        ( 'APP.app.template', '%s.app'%(appName.upper()) ),
        ( 'APP_init.php.in.template', '%s_init.php.in'%(appName.upper()) )
    ]

    toParseFiles = [
        '%s.app'%(appName.upper())
    ]

    # create tmp dir
    tempDir = mkdtemp()
    #print "working in %s"%(tempDir)
    # copy files to tmp dir
    copytree(os.path.join(templateDir, 'APP'), tempDir, symlinks=False)
    # rename files in tmp dir
    for (fromFilePath, toFilePath) in toMoveFiles:
        fromFileFullPath = os.path.join(tempDir, fromFilePath)
        toFileFullPath = os.path.join(tempDir,toFilePath)
        #print "move %s to %s"%(fromFileFullPath, toFileFullPath)
        shutil.move(fromFileFullPath, toFileFullPath)
    # parse files in tmp dir
    for parsedFilePath in toParseFiles:
        parsedFileFullPath = os.path.join(tempDir, parsedFilePath)
        #print "parsing %s"%(parsedFileFullPath)

        for line in fileinput.input(parsedFileFullPath, inplace=1):
            print Template(line).safe_substitute({
            'APPNAME': appName.upper(),
            'CHILDOF': childOf.upper(),
            'appShortName': appShortName,
            'appIcon': "%s.png"%(appName.lower())
        }).rstrip() #strip to remove EOL duplication

    # move tmp dir to target dir
    shutil.move(tempDir, os.path.join(targetDir, appName.upper()))
    return

def main():
    args = parseOptions()
    addApplication(
        args.appName,
        childOf = args.childOf,
        appShortName = args.appName.capitalize(),
        templateDir = args.templateDir,
        targetDir = args.targetDir)

if __name__ == "__main__":
    main()