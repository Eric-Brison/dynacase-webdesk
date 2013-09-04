pages_mo = $(patsubst %.po,$(localedir)/en/LC_MESSAGES/%.mo,$(wildcard *.po))

include $(utildir)/PubRule

$(localedir)/en/LC_MESSAGES/%.mo:  /var/tmp/%.gmo  $(localedir)/en/LC_MESSAGES $(wildcard *.po)
	$(installcp) $< $@
