######################################################################
# Simple example
# (C) 2006 Tail-f Systems
#
# See the README file for more information
######################################################################

usage:
	@echo "See README file for more instructions"
	@echo "make all      Build all example files"
	@echo "make clean    Remove all built and intermediary files"
	@echo "make start    Start ConfD daemon and example agent"
	@echo "make stop     Stop any ConfD daemon and example agent"
	@echo "make query    Run NETCONF query against ConfD"
	@echo "make cli      Start the ConfD Command Line Interface"

######################################################################
# Where is ConfD installed? Make sure CONFD_DIR points it out
CONFD_DIR ?= ../../..

# Include standard ConfD build definitions and rules
include $(CONFD_DIR)/src/confd/build/include.mk

# In case CONFD_DIR is not set (correctly), this rule will trigger
$(CONFD_DIR)/src/confd/build/include.mk:
	@echo 'Where is ConfD installed? Set $$CONFD_DIR to point it out!'
	@echo ''
	@exit 1

######################################################################
# Example specific definitions and rules

CONFD_FLAGS = --addloadpath $(CONFD_DIR)/etc/confd
START_FLAGS ?=

SRC	  = simple_withtrans.c
OBJS	  = $(SRC:.c=.o)

######################################################################
all:	smp.fxs smp.h simple_withtrans $(CDB_DIR) ssh-keydir
	@echo "Build complete"

simple_withtrans: $(OBJS)
	$(CC) -o $@ $(OBJS) $(LIBS)

######################################################################
clean:	iclean
	-rm -rf smp.h simple_withtrans running.* 2> /dev/null || true


start:  stop
	$(CONFD) -c ./confd.conf $(CONFD_FLAGS)
	### * In one terminal window, run: tail -f ./confd.log
	### * In another terminal window, run queries
	###   (try 'make query' for an example)
	### * In this window, the HOSTS confd daemon now starts:
	./simple_withtrans

######################################################################
stop:
	### Killing any confd daemon and simple_withtrans confd agents
	$(CONFD) --stop          || true
	$(KILLALL) simple_withtrans || true

######################################################################
cli:
	$(CONFD_DIR)/bin/confd_cli --user=admin --groups=admin \
		--interactive || echo Exit

######################################################################
cli-c:
	$(CONFD_DIR)/bin/confd_cli -C --user=admin --groups=admin \
		--interactive || echo Exit

######################################################################
query:
	$(CONFD_DIR)/bin/netconf-console cmd-get-servers.xml

######################################################################
