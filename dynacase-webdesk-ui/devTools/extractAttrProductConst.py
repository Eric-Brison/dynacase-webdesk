#! /usr/bin/env python
# -*- coding: utf-8 -*-

import os
import codecs
import string
import argparse
import logging

logging.basicConfig(
    format='%(levelname)s:%(message)s'
)

class MethodStructException(Exception):
    def __init__(self, value):
        self.value = value
    def __str__(self):
        return repr(self.value)

def parseOptions():
    argParser = argparse.ArgumentParser(
        description = 'inserts constants for attributes and parameters in Method files.',
        formatter_class = argparse.ArgumentDefaultsHelpFormatter
    )
    argParser.add_argument('familiesFolder',
        help = 'families folder',
        nargs = '?',
        default = os.path.relpath(os.path.join(os.path.dirname(__file__), '..', 'Families')))
    argParser.add_argument('--familyFile',
        help = 'family csv file (use it several times to parse several csv files)',
        action = 'append',
        dest = 'familyCsvFiles'),
    argParser.add_argument('--wflFile',
        help = 'workflow csv file (use it several times to parse several csv files)',
        action = 'append',
        dest = 'wflCsvFiles'),
    argParser.add_argument('--begin-keyword',
        help = 'keyword opening attributes area',
        dest = 'beginKw',
        default = '//region attributes-constants'),
    argParser.add_argument('--begin-keyword-new',
        help = 'keyword opening attributes area',
        dest = 'beginKwNew',
        default = '//region attributes-constants'),
    argParser.add_argument('--end-keyword',
        help = 'keyword closing attributes area',
        dest = 'endKw',
        default = '//endregion'),
    argParser.add_argument('--end-keyword-new',
        help = 'keyword closing attributes area',
        dest = 'endKwNew',
        default = '//endregion'),
    argParser.add_argument('--logLevel',
        help = 'logging level',
        dest = 'logLevel',
        choices=['DEBUG', 'INFO', 'WARNING', 'ERROR', 'CRITICAL'],
        default = 'WARNING')
    args = argParser.parse_args()
    return args

def getFamilyFiles(familiesFolder):
    familyFiles = []
    for root, dirs, files in os.walk(familiesFolder):
        for fileName in files:
            if (string.lower(os.path.splitext(fileName)[1]) in ['.csv']) and (fileName[:7] == "STRUCT_") :
                familyFiles.append(os.path.join(root, fileName))
    logging.info("found %s family files", len(familyFiles))
    return familyFiles

def getWflFiles(familiesFolder):
    wflFiles = []
    for root, dirs, files in os.walk(familiesFolder):
        for fileName in files:
            if (string.lower(os.path.splitext(fileName)[1]) in ['.csv']) and (fileName[:4] == "WFL_") :
                wflFiles.append(os.path.join(root, fileName))
    logging.info("found %s workflow files", len(wflFiles))
    return wflFiles

def buildFileContent(methodFileName, attributes, args):
    methodFile = codecs.open(methodFileName, 'r', 'utf8')
    methodContent = []
    modeInAttr = False
    attributesInjected = False
    for currentContent in methodFile.readlines():
        if(not modeInAttr):
            beginKwPosition = currentContent.find(args.beginKw)
            if(beginKwPosition>=0):
                if attributesInjected:
                    logging.warning("%s found multiple times", args.beginKw)
                else:
                    modeInAttr = True
                    if(args.beginKw != args.beginKwNew):
                        currentContent = currentContent.replace(args.beginKw, args.beginKwNew)
                    methodContent.append(currentContent)
                    indent = ' '*beginKwPosition
                    for currentAttrId in attributes:
                        #TODO: are \n required?
                        methodContent.append("%s/** %s */\n"%(indent, attributes[currentAttrId]))
                        methodContent.append("%sconst %s = '%s';\n"%(indent, currentAttrId, currentAttrId))
                    #prevent injection from happening twice
                    attributesInjected = True
            else:
                methodContent.append(currentContent)
        else:
            if(args.endKw in currentContent):
                modeInAttr = False
                if(args.endKw != args.endKwNew):
                        currentContent = currentContent.replace(args.endKw, args.endKwNew)
                methodContent.append(currentContent)
    methodFile.close()
    if(not attributesInjected):
        raise MethodStructException("%s not written: %s not found"%(methodFileName, args.beginKw))
    elif(modeInAttr):
        raise MethodStructException("%s not written: %s not found"%(methodFileName, args.endKw))
    else:
        return methodContent

def extractFamilyAttr(directory, structFileName, args):
    paramFileName = os.path.join(os.path.dirname(structFileName), "PARAM_" + os.path.basename(structFileName)[7:])
    if(not os.path.isfile(paramFileName)):
        logging.debug("skipping %s since it does not exists", paramFileName)
    else:
        methodFileName = ''
        paramReader = codecs.open(paramFileName, 'r', 'utf8').readlines()
        for currentLine in paramReader:
            currentLine = currentLine.split(";")
            if currentLine[0] == "METHOD":
                if( (currentLine[1][0] != '*') and (currentLine[1][0] != '+') ):
                    methodFileName = currentLine[1]
                    break

        structReader = codecs.open(structFileName, 'r', 'utf8').readlines()
        attributes = {}
        for currentLine in structReader:
            currentLine = currentLine.split(";")
            if currentLine[0] == "ATTR":
                attributes[currentLine[1].lower()] = currentLine[3]
            if currentLine[0] == "PARAM":
                attributes[currentLine[1].lower()] = '<PARAMETER> ' + currentLine[3]
            if currentLine[0] == "METHOD":
                if( (currentLine[1][0] != '*') and (currentLine[1][0] != '+') ):
                    if( (methodFileName != '') and (methodFileName != currentLine[1]) ):
                        # revert to empty string so that this csv is not parsed
                        methodFileName = ''
                        logging.error("duplicate method declaration for %s | %s", structFileName, paramFileName)
                        break
                    methodFileName = currentLine[1]

        if(methodFileName == ''):
            logging.warning("skipping %s | %s since their method declaration is eroneous", structFileName, paramFileName)
        else:
            methodFileName = os.path.join(directory, methodFileName)
            if(not os.path.isfile(methodFileName)):
                logging.warning("method file %s for %s | %s does not exists", methodFileName, structFileName, paramFileName)
            else:
                logging.debug("working on %s for %s | %s", methodFileName, structFileName, paramFileName)
                try:
                    methodContent = buildFileContent(methodFileName, attributes, args)
                    if(len(methodContent) > 0):
                        methodFile = codecs.open(methodFileName, 'w', 'utf8')
                        methodFile.writelines(methodContent)
                        methodFile.close()
                        logging.debug("%s attributes written in %s for %s | %s ", len(attributes), os.path.basename(methodFileName), os.path.basename(structFileName), os.path.basename(paramFileName))
                    else:
                        logging.debug("Nothing to write in %s for %s | %s ", len(attributes), os.path.basename(methodFileName), os.path.basename(structFileName), os.path.basename(paramFileName))
                except MethodStructException as e:
                    logging.error(e.value)

def extractWflAttr(directory, wflFileName, args):
        structReader = codecs.open(wflFileName, 'r', 'utf8').readlines()
        attributes = {}
        for currentLine in structReader:
            currentLine = currentLine.split(";")
            if currentLine[0] == "ATTR":
                attributes[currentLine[1].lower()] = currentLine[3]
            if currentLine[0] == "PARAM":
                attributes[currentLine[1].lower()] = currentLine[3]
            if currentLine[0] == "BEGIN":
                if(currentLine[4] != ''):
                    classFileName = currentLine[4]
                else:
                    classFileName = ''
                    logging.error("no className for %s", wflFileName)
                    break

        if(classFileName == ''):
            logging.debug("skipping %s", wflFileName)
        else:
            classFileName = os.path.join(directory, classFileName)
            if(not os.path.isfile(classFileName)):
                logging.error("class file %s for %s does not exists", classFileName, wflFileName)
            else:
                logging.debug("working on %s for %s", classFileName, wflFileName)
                classContent = buildFileContent(classFileName, attributes, args)
                if(len(classContent) > 0):
                    classFile = codecs.open(classFileName, 'w', 'utf8')
                    classFile.writelines(classFileName)
                    classFile.close()
                    logging.debug("%s attributes written in %s for %s", len(attributes), os.path.basename(classFileName), os.path.basename(wflFileName))

def main():
    args = parseOptions()

    try:
        logLevel = eval("logging."+args.logLevel)
        logging.getLogger().setLevel(logLevel)
        logging.info("log level set to %s (%s)"%(args.logLevel, logLevel))
    except:
        logging.getLogger().setLevel(logging.INFO)
        logging.error("log level fallback to INFO")


    if(not args.familyCsvFiles):
        args.familyCsvFiles = getFamilyFiles(args.familiesFolder)
    if(not args.wflCsvFiles):
        args.wflCsvFiles = getWflFiles(args.familiesFolder)

    for fileName in args.familyCsvFiles:
        extractFamilyAttr(args.familiesFolder, fileName, args)
    for fileName in args.wflCsvFiles:
        extractFamilyAttr(args.familiesFolder, fileName, args)

if __name__ == "__main__":
    main()
