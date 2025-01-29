define NEWLINE


endef # Wake up, this isn't a dream.

rwildcard = $(foreach d,$(wildcard $(1:=/*)),$(call rwildcard,$d,$2) $(filter $(subst *,%,$2),$d))

SRC = $(call rwildcard,src,*.c *.h)
SRC_LIB = $(call rwildcard,lib,*.c *.h)
SRC_TEST = $(call rwildcard,test, *.c *.h)

# raphael.env : config db locale de Raphael
# .env : config db serveur
ENV_FILE = raphael.env

CC = gcc
LFLAGS = -ljson-c -I/usr/include/postgresql -L/usr/lib/x86_64-linux-gnu -lpq
CFLAGS = -Wall -Wextra -pedantic -fanalyzer -std=c23 -isystem /work/lib -D$(subst $(NEWLINE), -D,$(file <../../../include/$(ENV_FILE)))

CFLAGS_DEBUG = -g -Og -fsanitize=address
CFLAGS_RELEASE = -DNDEBUG

CONFIG ?= debug
ifeq ($(CONFIG), debug)
	CFLAGS += $(CFLAGS_DEBUG)
else
	CFLAGS += $(CFLAGS_RELEASE)
endif

.PHONY: debug release all test clean debug

debug: CONFIG=debug all
release: CONFIG=relase all

all: bin/act

test: bin/test/test
	bin/test/test

clean:
	rm -r bin

bin/act: act.c $(SRC) $(SRC_LIB)
	./docker-gcc -o $@ -c '$(CFLAGS)' -l '$(LFLAGS)' $^

bin/test/test: test/test.c $(SRC) $(SRC_LIB) $(SRC_TEST)
	./docker-gcc -o $@ -c '$(CFLAGS)' -l '$(LFLAGS) -lm' $^
