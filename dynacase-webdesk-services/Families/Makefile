# ============================================
# $Id: Makefile,v 1.1 2010-01-13 10:47:16 eric Exp $
#
# ============================================

SUBDIR= Views Externals

include $(utildir)/PubRule

pages_fdl = $(patsubst %.php,$(pubdir)/$(applib)/%.php,$(wildcard *.php))

pages_csv = $(patsubst %.csv,$(pubdir)/$(style)/$(appname)/%.csv,$(filter-out $(pages_not_csv),$(wildcard *.csv)))
pages_xml = $(patsubst %.xml,$(pubdir)/$(style)/$(appname)/%.xml,$(filter-out $(pages_not_xml),$(wildcard *.xml)))

$(pubdir)/$(applib):
	mkdir $@

$(pubdir)/$(applib)/%.php: %.php $(pubdir)/$(applib)
	cd $(pubdir)/$(applib); \
	ln -sf ../$(appname)/$< .

$(pubdir)/$(style)/$(appname)/%.csv : %.csv $(pubdir)/$(style)/$(appname)
	$(installcp) $< $@

$(pubdir)/$(style)/$(appname)/%.xml : %.xml $(pubdir)/$(style)/$(appname)
	$(installcp) $< $@

publish: $(pubdir)/$(applib) $(pages_fdl) $(pages_csv) $(pages_xml)