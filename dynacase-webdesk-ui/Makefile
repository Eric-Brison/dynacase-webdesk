appname=$(appname)

export appname PACKAGE

SUBDIR= Apps Actions Scripts Images Families locale

.PHONY : publish clean stub

include $(utildir)/PubRule

STUB = Families/stubWebdeskUiFamilies.php
STUB_CSV = Families/PortalService.csv Families/UserPortal.csv

Families/stubWebdeskUiFamilies.php: ${STUB_CSV}
	php ${utildir}/genStubFamilyClass.php ${STUB_CSV} > $@

stub: ${STUB}
