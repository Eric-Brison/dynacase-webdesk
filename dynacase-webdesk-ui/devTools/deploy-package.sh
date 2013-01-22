#!/bin/bash
# V1.0

BLUE="\033[0;34m"
RED="\033[0;31m"
GREEN="\033[0;32m"
BLACK="\033[0;00m"

usage="$0 [-c <config file>] [-d <source directory>] [-p Y|N] [-q (0|1|2)] [-w] [-y]"
usage="${usage}\n\t\ndefaults is -c \`pwd\`/deploy-package.config -d \`pwd\` -p Y -q 0"
usage="${usage}\n\t-d specifies where sources are"
usage="${usage}\n\t-y answer 'yes' to questions"
usage="${usage}\n\t-w waits before exiting"
usage="${usage}\n\t-p Y makes building po files"
usage="${usage}\n\t-q is quiet level (higher is quiter)"

SCRIPT_PATH=`readlink -f $(dirname $0)`
BUILD_PACKAGE_COMMAND=${SCRIPT_PATH}/build-package.sh
SOURCE_DIR=`pwd`
TMP_DIR=`mktemp -d`
WRITE_CONFIG=false

waitOnExit=false
quiet=0
forceyes=false
errors=false
seems_valid_wiff_dir=false
seems_valid_context=false
makepo="N"

echo_2() { if [ ${quiet} -lt 2 ]; then echo -e $1; fi;}

validate_wiff_dir(){
    if [ -z "${wiff_dir_path_input}" ]; then
        seems_valid_wiff_dir=false
        return 0
    elif [ ! -d "${wiff_dir_path_input}" ]; then
        echo "${wiff_dir_path} does not seems to be a valid wiff dir path"
        seems_valid_wiff_dir=false
        return 0
    fi

    if [ -f "${wiff_dir_path_input}/wiff.php" -a -f "${wiff_dir_path_input}/wiff" ]; then
        wiff_dir_path=${wiff_dir_path_input}
        seems_valid_wiff_dir=true
        return 0
    fi

    seems_valid_wiff_dir=false
    return 1
}

save_config(){
    echo "################################################" > ${configfile}
    echo "#" >> ${configfile}
    echo "#  date de génération: $(date)" >> ${configfile}
    echo "#" >> ${configfile}
    echo "################################################" >> ${configfile}
    echo "" >> ${configfile}
    echo "wiff_dir_path='${wiff_dir_path}'" >> ${configfile}
    echo "target_context='${target_context}'" >> ${configfile}
}

initcontexts(){
    OLDIFS=$IFS
    IFS=$'\012'
    contexts=( $(sudo ${wiff_dir_path}/wiff list context) )
    IFS=${OLDIFS}
    rangcontext=0
    for context in "${contexts[@]}"; do
        acontexts[${rangcontext}]=${context}
        if [ "${context}" = "${target_context}" ]; then
            defaultrang=${rangcontext}
        fi
        rangcontext=$((${rangcontext}+1))
    done
}

findcontext(){
    if ${forceyes}; then
        if [ -z "${target_context}" ]; then
            echo "there is no default context"
        elif [ -z "${defaultrang}" ]; then
            echo "the default context (${target_context}) is no more available"
            askcontext
        else
            seems_valid_context=true
        fi
    else
        askcontext
    fi
}

askcontext(){
    echo "--- Contextes disponibles ---"
    for (( i = 0 ; i < ${#acontexts[@]} ; i++ )); do
        if [ -z "${defaultrang}" ]; then
            thisisdefault=''
        elif [ ${i} -eq ${defaultrang} ]; then
            thisisdefault="\t<-- default value"
        else
            thisisdefault=''
        fi
        echo -e "\t[${i}] ${acontexts[${i}]}${thisisdefault}"
    done
    read -e -p "Dans quel contexte souhaitez-vous publier? [${defaultrang}] " rangcontext_input
    if [ -z "${rangcontext_input}" ]; then
        rangcontext_input=${defaultrang}
    fi
    if [ ${rangcontext_input} -gt ${#acontexts[@]} ]; then
        echo "Veuillez saisir un nombre entre 0 et ${#acontexts[@]}"
        return 1
    fi
    target_context=${acontexts[${rangcontext_input}]}
    seems_valid_context=true
}

install_webinst(){
    if [ -z "$1" ]; then
        return 1
    fi
    if [ -z "${target_context}" ]; then
        return 1
    fi
    sudo "${wiff_dir_path}/wiff" context "${target_context}" module install --force "$1" 2> /dev/null
    return $?
}

upgrade_webinst(){
    if [ -z "$1" ]; then
        return 1
    fi
    if [ -z "${target_context}" ]; then
        return 1
    fi
    sudo "${wiff_dir_path}/wiff" context "${target_context}" module upgrade --force "$1" 2> /dev/null
    return $?
}

deploy_webinst(){
    if [ -z "$1" ]; then
        return 1
    fi
    basewebinst=$(basename $1 .webinst)
    modulename=`expr match "${basewebinst}" '^\(.*\)-[0-9][0-9.]*-[0-9][0-9]*$'`
    if [ -z ${modulename} ]; then
        modulename=`expr match "${basewebinst}" '^\(.*\)-[0-9][0-9.]*'`
    fi
    echo "searching if ${modulename} is installed (from ${basewebinst})"
    installedmodule=`sudo "${wiff_dir_path}/wiff" context "${target_context}" module list installed 2>/dev/null | grep "${modulename}"`
    grepstatus=$?
    if [ ${grepstatus} -eq 0 ]; then
        echo -e "${installedmodule} detected.\n\t${BLUE} UPGRADE with $1 ${BLACK}"
        upgrade_webinst "$1"
    elif [ ${grepstatus} -eq 1 ]; then
        echo -e "${modulename} not detected.\n\t${BLUE} INSTALLATION with $1 ${BLACK}"
        install_webinst "$1"
    else
        echo "an error occured when detecting if ${modulename} is installed. Installed modules are:"
        sudo "${wiff_dir_path}/wiff" context "${target_context}" module list installed 2>/dev/null
        errors=true
    fi
    
    return $?
}

while getopts ":c:d:p:q:wyh" opt; do
    case ${opt} in
        c)
            configfile=${OPTARG}
            ;;
        d)
            SOURCE_DIR=$(readlink -f "${OPTARG}")
            ;;
        p)
            if [ "${OPTARG}" == "Y" ]; then
                makepo="Y"
            elif [ "${OPTARG}" == "N" ]; then
                makepo="N"
            else
                echo_2 "invalid value for -p (${OPTARG}). The default will be used (N)"
            fi
            ;;
        q)
            quiet=${OPTARG}
            ;;
        w)
            waitOnExit=true
            ;;
        y)
            forceyes=true
            ;;
        h)
            echo -e ${usage}
            exit 0
            ;;
        \?)
            echo "Invalid option: -${OPTARG}" >&2
            echo -e ${usage}
            exit 1
            ;;
        :)
            echo "Option -${OPTARG} requires an argument." >&2
            echo -e ${usage}
            exit 1
        ;;
    esac
done

shopt -s nullglob

if [ -z "${configfile}" ]; then
    configfile="${SCRIPT_PATH}/deploy-package.config"
fi
configdir=`readlink -f $(dirname ${configfile})`

if [ -w "${configfile}" ]; then
    WRITE_CONFIG=true
elif [ -f "${configfile}" ]; then
    echo "${configfile} is not writable. it will be used read-only."
    WRITE_CONFIG=false
elif [ -w ${configdir} ]; then
    echo "${configfile} does not exists. it will be created."
    touch ${configfile}
    WRITE_CONFIG=true
else
    echo "${configfile} does not exixst and ${configdir} is not writable."
    WRITE_CONFIG=false
fi

if [ -f ${configfile} ]; then
    . ${configfile} 2> /dev/null
    if [ $? -gt 0 ]; then
        echo_2 "an error occured when sourcing ${configfile}"
    fi
fi

if ${forceyes}; then
    if [ -z "${wiff_dir_path}" ]; then
        echo "there is no default wiff dir path"
    else
        wiff_dir_path_input=${wiff_dir_path}
        validate_wiff_dir
        if ! ${seems_valid_wiff_dir}; then
            echo "the default wiff dir path (${wiff_dir_path}) is not valid"
        fi
    fi
fi
while ! ${seems_valid_wiff_dir}; do
    read -e -p "please specify wiff directory path [${wiff_dir_path}] " wiff_dir_path_input
    if [ -z "${wiff_dir_path_input}" ]; then
        wiff_dir_path_input=${wiff_dir_path}
    fi
    validate_wiff_dir
done

initcontexts

if ! ${seems_valid_context}; then
    findcontext
fi
while ! ${seems_valid_context}; do
    askcontext
done

if ${WRITE_CONFIG}; then
    save_config
    if [ $? -gt 0 ]; then
        echo_2 "an error occured when trying to save config to ${configfile}"
        echo_2 "current config will only be used for this session"
    fi
fi

chmod 777 ${TMP_DIR}

if [ -f "${BUILD_PACKAGE_COMMAND}" ]; then
    if  [ -x "${BUILD_PACKAGE_COMMAND}" ]; then
        "${BUILD_PACKAGE_COMMAND}" -d ${SOURCE_DIR} -o ${TMP_DIR} -q 2 -p ${makepo}
        buildstatus=$?
        if [ ${buildstatus} -gt 0 ]; then
            echo "an error occured in webinst generation"
            rm -rf ${TMP_DIR}
            exit $((${buildstatus}+1))
        fi
    else
        echo "${BUILD_PACKAGE_COMMAND} is not executable"
        rm -rf ${TMP_DIR}
        exit 1
    fi
else
    echo "${BUILD_PACKAGE_COMMAND} does not exists"
    rm -rf ${TMP_DIR}
    exit 1
fi

for webinst in ${TMP_DIR}/*.webinst; do
    deploy_webinst ${webinst}
done

deploystatus=$?
if [ ${deploystatus} -gt 0 ]; then
    echo "an error occured in webinst deployment"
    errors=true
else
    rm -rf ${TMP_DIR}
    rmstatus=$?
    if [ ${rmstatus} -gt 0 ]; then
        echo "an error occured when deleting ${TMP_DIR}"
        errors=true
    fi
fi

if ${errors}; then
    echo -e "${RED} The script ended with errors.${BLACK}"
else
    echo_2 "${GREEN} the script ended with success.${BLACK}"
fi

if ${waitOnExit}; then
    read -p "press enter to exit" exitkey
fi

if ${errors}; then
    exit 1
else
    exit 0
fi
