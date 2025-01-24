define NEWLINE


endef # Wake up, this isn't a dream.

SRC = $(wildcard src/*)
TEST_LIB = $(wildcard test/lib/*)

# raphael.env : config db locale de Raphael
# .env : config db serveur
ENV_FILE = raphael.env

CC = gcc
LFLAGS = -ljson-c -I/usr/include/postgresql -L/usr/lib/x86_64-linux-gnu -lpq
CFLAGS = -Wall -Wextra -pedantic -std=c23 -D$(subst $(NEWLINE), -D,$(file <../../../include/$(ENV_FILE)))

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

test: bin/test/uuid
	bin/test/uuid

clean:
	rm -r bin

bin/act: act.c $(SRC)
	./docker-gcc -o $@ -c '$(CFLAGS) -fanalyzer' -l '$(LFLAGS)' $^

bin/test/uuid: test/uuid.c $(SRC) $(TEST_LIB)
	./docker-gcc -o $@ -c '$(CFLAGS)' -l '$(LFLAGS) -lm' $^
