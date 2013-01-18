#! /usr/bin/env python
# -*- coding: utf-8 -*-

from string import Template
import sys
import os

if sys.version_info < (2, 7):
    raise "must use python 2.7 or greater"
import argparse

def parseOptions():
    argParser = argparse.ArgumentParser(
        description='Generate workflow. (requires python >= 2.7)'
    )
    argParser.add_argument('-n', '--name',
        help = 'family logical name',
        dest = 'familyName')
    argParser.add_argument('--templateDir',
        help = 'templates directory',
        dest = 'templateDir',
        default = os.path.join(os.path.dirname(__file__), 'templates'))
    argParser.add_argument('--targetDir',
        help = 'target directory, where generated files will be placed',
        dest = 'targetDir',
        default = os.path.join(os.path.dirname(__file__), '..', 'Families'))
    argParser.add_argument('--force',
        help = 'overwrite existing files',
        action = 'store_true',
        dest = 'force',
        default = False)
    args = argParser.parse_args()
    if(not args.familyName):
        args.familyName = raw_input("Give me your logical Name : ")
    return args

def getWflMemo(templateValues):
    importStr = """
    <process command="./wsh.php --api=importDocuments --file=./@APPNAME@/WFL_$familyName.csv">
        <label lang="en">importing WFL_$familyName.csv</label>
    </process>"""
    return Template(importStr).safe_substitute(familyName = templateValues['familyName'].lower())

def generateWorkflow(templateValues, args):
    templateValues['workflowName'] = "WFL_%s"%(templateValues['familyName'].upper())
    targetsPath ={
        'wflCsv': os.path.join(args.targetDir, "WFL_%s.csv"%(args.familyName.lower())),
        'wflPhp': os.path.join(args.targetDir, "Class.%s.php"%(templateValues['workflowName']))
    }

    if(not args.force):
        overwrittenFiles = 0
        for targetPath in targetsPath:
            if(os.path.exists(targetsPath[targetPath])):
                overwrittenFiles += 1
                print "existingt file %s would be overwritten. please use --force to allow this"%(targetsPath[targetPath])
        if(overwrittenFiles > 0):
            raise NameError("overwriting %s files"%(overwrittenFiles))

    templateFilesPath ={
        'wflCsv': os.path.join(args.templateDir, "WFL_workflow.csv.template"),
        'wflPhp' : os.path.join(args.templateDir, "Class.workflow.php.template")
    }

    templates = {}
    for fileDesignation in templateFilesPath:
        templates[fileDesignation] = Template(open(templateFilesPath[fileDesignation]).read())

    for target in targetsPath:
        if templateFilesPath.has_key(target):
            template = Template(open(templateFilesPath[target]).read())
            targetString = template.safe_substitute(templateValues)
            targetFile = open(targetsPath[target], 'w')
            targetFile.write(targetString)
            targetFile.close()
        else:
            print "no template found for %s"%(target)

def main():
    args = parseOptions()

    templateValues = {
        'familyName' : args.familyName.upper()
    }

    try:
        generateWorkflow(templateValues, args)
        print(getWflMemo(templateValues))
    except NameError:
        return

if __name__ == "__main__":
    main()
    print ""