# ============================================
# $Id: Makefile,v 1.1 2010-01-13 10:47:17 eric Exp $
#
# ============================================


include $(utildir)/PubRule

pages_fdl = $(patsubst %.php,$(pubdir)/$(applib)/%.php,$(wildcard *.php))

$(pubdir)/API:
	mkdir -p $@

$(pubdir)/$(applib)/%.php: %.php $(pubdir)/API
	cd $(pubdir)/API; \
	ln -sf ../$(appname)/$< .

publish: $(pubdir)/API $(pages_fdl) 