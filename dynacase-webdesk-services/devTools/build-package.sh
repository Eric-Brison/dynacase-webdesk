#!/bin/bash
# V 1.0

BLUE="\033[0;34m"
RED="\033[0;31m"
GREEN="\033[0;32m"
BLACK="\033[0;00m"

usage="$0 [-o <output directory>] [-d <source directory>] [-q (0|1|2)] [-p (Y|N)] [-w (Y|N)]"
usage="${usage}\n\tdefaults is -o \`pwd\` -d \`pwd\` -q 0 -p N -w Y"
usage="${usage}\n\t-d specifies where sources are"
usage="${usage}\n\t-p Y makes building po files"
usage="${usage}\n\t-w Y makes building webinst files"
usage="${usage}\n\t-q is quiet level (higher is quiter)"

SCRIPT_PATH=`readlink -f $(dirname $0)`
SOURCE_DIR=`pwd`
TMP_DIR=`mktemp -d`

quiet=0
makepo=false
makewebinst=true
errors=false

echo_2() { if [ ${quiet} -lt 2 ]; then echo $1; fi;}

while getopts ":o:p:q:w:d:h" opt; do
    case ${opt} in
        o)
            outputdir=${OPTARG}
            ;;
        d)
            SOURCE_DIR=$(readlink -f "${OPTARG}")
            ;;
        p)
            if [ "${OPTARG}" == "Y" ]; then
                makepo=true
            elif [ "${OPTARG}" == "N" ]; then
                makepo=false
            else
                echo_2 "invalid value for -p (${OPTARG}). The default will be used (N)"
            fi
            ;;
        q)
            quiet=${OPTARG}
            ;;
        w)
            if [ "${OPTARG}" == "Y" ]; then
                makewebinst=true
            elif [ "${OPTARG}" == "N" ]; then
                makewebinst=false
            else
                echo_2 "invalid value for -w (${OPTARG}). The default will be used (Y)"
            fi
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

if [ -z "${outputdir}" ]; then
    outputdir=${SOURCE_DIR}
fi

if [ ! -d "${outputdir}" ]; then
    echo "${outputdir} n'est pas un répertoire"
    exit 1
fi

if [ ! -w "${outputdir}" ]; then
    echo "le répertoire ${outputdir} ne possède pas les droits d'écriture"
    exit 1
fi

tar --exclude='*.webinst' -C ${SOURCE_DIR} -cf - . | tar -C ${TMP_DIR} -xf -
if [ -f "${SOURCE_DIR}/info.xml.in.dev" ]; then
    cp -f "${SOURCE_DIR}/info.xml.in.dev" "${TMP_DIR}/info.xml.in"
fi
cd ${TMP_DIR}

LOG_FILE="${outputdir}/$(basename $0 .sh)-$(date +%Y%m%d).log"

echo '' >> ${LOG_FILE}
echo '###########################' >> ${LOG_FILE}
echo '#                                                 #' >> ${LOG_FILE}
echo "#     $(date +%x) $(date +%X)     #" >> ${LOG_FILE}
echo '#                                                 #' >> ${LOG_FILE}
echo '###########################' >> ${LOG_FILE}
echo '' >> ${LOG_FILE}
echo "        Output directory: ${outputdir}" >> ${LOG_FILE}
echo "        tmp directory: ${TMP_DIR}" >> ${LOG_FILE}
echo "        log file: ${LOG_FILE}" >> ${LOG_FILE}
echo '' >> ${LOG_FILE}
echo '###########################' >> ${LOG_FILE}
echo '' >> ${LOG_FILE}

make clean &> /dev/null

echo '' >> ${LOG_FILE}
echo '=== autoconf ===' >> ${LOG_FILE}
echo '' >> ${LOG_FILE}
autoconf &>> ${LOG_FILE}
echo '' >> ${LOG_FILE}
echo "--- autoconf exitcode: $? ---" >> ${LOG_FILE}

echo '' >> ${LOG_FILE}
echo '=== ./configure ===' >> ${LOG_FILE}
echo '' >> ${LOG_FILE}
./configure &>> ${LOG_FILE}
echo '' >> ${LOG_FILE}
echo "--- ./configure exitcode: $? ---" >> ${LOG_FILE}

if ${makepo}; then
    echo '' >> ${LOG_FILE}
    echo '=== make po ===' >> ${LOG_FILE}
    echo '' >> ${LOG_FILE}
    make po &>> ${LOG_FILE}
    echo '' >> ${LOG_FILE}
    echo "--- make po exitcode: $? ---" >> ${LOG_FILE}

    nbpo=0
    for po in ${TMP_DIR}/*.po; do
        nbpo=$((${nbpo}+1))
        cp ${po} ${outputdir}
        cp ${po} ${SOURCE_DIR}
    done

    if [ ${nbpo} -gt 0 ]; then
        if [ ${quiet} -lt 2 ]; then
            echo "${nbpo} po(s): "
            for po in ${outputdir}/*.po; do
                echo -e "\t${po}"
            done
            echo -e "\tpo were copied in source dir:${SOURCE_DIR}"
        fi
    else
        echo "no po builded"
        errors=true
    fi
fi

if ${makewebinst}; then
    echo '' >> ${LOG_FILE}
    echo '=== make webinst ===' >> ${LOG_FILE}
    echo '' >> ${LOG_FILE}
    make webinst &>> ${LOG_FILE}
    makewebinstexitcode=$?
    echo '' >> ${LOG_FILE}
    echo "--- make webinst exitcode: ${makewebinstexitcode} ---" >> ${LOG_FILE}

    if [ ${makewebinstexitcode} -eq 0 ]; then
        nbwebinst=0
        for webinst in ${TMP_DIR}/*.webinst; do
            nbwebinst=$((${nbwebinst}+1))
            cp ${webinst} ${outputdir}
        done

        if [ ${nbwebinst} -gt 0 ]; then
            if [ ${quiet} -lt 2 ]; then
                echo "${nbwebinst} webinst(s): "
                for webinst in ${outputdir}/*.webinst; do
                    echo -e "\t${webinst}"
                done
            fi
        else
            echo "0 webinst builded"
            errors=true
        fi
    else
        echo -e "${RED} no webinst builded (error generated from make webinst)${BLACK}"
        errors=true
    fi
fi

if ${errors}; then
    echo "log file is ${LOG_FILE}"
    echo "tmp dir is ${TMP_DIR}"
    exit 1
else
    rm -rf ${LOG_FILE}
    rm -rf ${TMP_DIR}
    exit 0
fi
