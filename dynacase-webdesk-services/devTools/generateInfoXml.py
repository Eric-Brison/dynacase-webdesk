#!/usr/bin/env python
# -*- coding: UTF-8 -*-

import xml.dom, xml.dom.minidom
import os
from xml.dom.ext import PrettyPrint
from StringIO import StringIO
import sys

if sys.version_info < (2, 7):
    raise "must use python 2.7 or greater"
import argparse

def toprettyxml_fixed (node, encoding='utf-8'):
    tmpStream = StringIO()
    PrettyPrint(node, stream=tmpStream, encoding=encoding)
    return tmpStream.getvalue()

def emptyNode(node):
    childNode = node.firstChild
    while (childNode):
        node.removeChild(childNode)
        childNode = node.firstChild
    return node

def addTarget(node, targetNode):
    localNode = node.ownerDocument.importNode(targetNode, True)
    node.appendChild(localNode.cloneNode(True))

def parseOptions():
    argParser = argparse.ArgumentParser(description='Generate info.xml file from targets. (requires PyXML and python >= 2.7)')
    argParser.add_argument('--to',
        help = 'info.xml target',
        default = os.path.join('..', 'info.xml.in'),
        dest = 'infoXmlFile',
        metavar = './info.xml.in')
    argParser.add_argument('--from',
        help = 'targets file',
        default = os.path.join(os.getcwd(), 'targets.xml'),
        dest = 'targetsFile',
        metavar = './targets.xml')
    argParser.add_argument('-p', '--phase',
        help = 'phases to generate',
        action = 'append',
        dest = 'phases',
        metavar = 'phases')
    argParser.add_argument('targetIds',
        help = 'target to include in produced file (use several times to add multiple targets',
        nargs = '*',
        metavar = 'targetId')
    args = argParser.parse_args()
    if(not args.phases):
        #args.phases = ['pre-install', 'post-install', 'pre-upgrade', 'post-upgrade']
        args.phases = ['post-install', 'post-upgrade']
    return args

def listTargets(targetsDom):
    print 'available targets are :'
    for target in targetsDom.getElementsByTagName('target'):
        print("\t- %s (%s)"%(target.getAttribute('id'), target.getAttribute('label')))

    print 'available profiles are :'
    for profile in targetsDom.getElementsByTagName('profile'):
        print("\t- %s (%s)"%(profile.getAttribute('id'), profile.getAttribute('label')))
        for refTarget in profile.getElementsByTagName('refTarget'):
            print("\t\t- %s"%(refTarget.getAttribute('targetId')))

    return(0)

def getNodes(document, phases):
    nodes = {}
    for phase in phases:
        nodeList = document.getElementsByTagName(phase)
        if(len(nodeList)):
            nodes[phase] = nodeList[0]
            emptyNode(nodes[phase])
        else:
            nodes[phase] = document.documentElement.appendChild(document.createElement(phase))
    return nodes

def appendTarget(nodes, targetNode, phases, targetsFile, visitedProfileIds):
    if(targetNode.nodeName == 'profile'):
        currentProfileId = targetNode.getAttribute('id')
        if(currentProfileId in visitedProfileIds):
            print >> sys.stderr, "profile %s#%s skipped because it is already inserted"%(targetsFile,currentProfileId)
        else:
            visitedProfileIds.append(currentProfileId)
            for refTarget in targetNode.getElementsByTagName('refTarget'):
                refTargetId = refTarget.getAttribute('targetId')
                refTargetNode = targetNode.ownerDocument.getElementById(refTargetId)
                if(not refTargetNode):
                    raise NameError('There is no target with this id: %s#%s'%(targetsFile,refTargetId))
                appendTarget(nodes, refTargetNode, phases, targetsFile, visitedProfileIds)
    else:
        useFor = targetNode.getAttribute('usefor')
        if(not useFor):
            useFor = phases
        for phase in phases:
            if(phase in useFor):
                commentNode = nodes[phase].ownerDocument.createComment('generated from target %s#%s (%s)'%(targetsFile, targetNode.getAttribute('id'), targetNode.getAttribute('label')))
                nodes[phase].appendChild(commentNode)
                for process in targetNode.childNodes:
                    localNode = nodes[phase].ownerDocument.importNode(process, True)
                    nodes[phase].appendChild(localNode)

def generateInfoXml(targetsFile, infoXmlFile, targetIds, phases):
    targetsDom = xml.dom.minidom.parse(open(targetsFile, 'r'))

    if(len(targetIds) == 0):
        return listTargets(targetsDom)

    infoXmlDom = xml.dom.minidom.parse(open(infoXmlFile, 'r'))
    nodes = getNodes(infoXmlDom, phases)

    for targetId in targetIds:
        visitedProfileIds = [] #avoid infinite loops
        targetNode = targetsDom.getElementById(targetId)
        if(not targetNode):
            raise NameError('There is no target with this id: %s#%s'%(targetsFile,targetId))

        appendTarget(nodes, targetNode, phases, targetsFile, visitedProfileIds)

    return toprettyxml_fixed(infoXmlDom)

def main():
    args = parseOptions()
    print generateInfoXml(args.targetsFile, args.infoXmlFile, args.targetIds, args.phases)


if __name__ == "__main__":
    main()
