define NEWLINE


endef # Wake up, this isn't a dream.

SRC=$(wildcard src/*)
TEST_LIB=$(wildcard test/lib/*)

.ENV=$(file <../../../include/.env)

CC=gcc
CFLAGS=-g -Og -Wall -Wextra -pedantic -std=c23 -D$(subst $(NEWLINE), -D,$(.ENV))
LFLAGS=-ljson-c -I/usr/include/postgresql -L/usr/lib/x86_64-linux-gnu -lpq

.PHONY: all test clean

all: bin/act

test: bin/test/uuid
	bin/test/uuid

clean:
	rm -r bin

bin/act: act.c $(SRC)
	./docker-gcc -o $@ -c '$(CFLAGS) -fanalyzer' -l '$(LFLAGS)' $^

bin/test/uuid: test/uuid.c $(SRC) $(TEST_LIB)
	./docker-gcc -o $@ -c '$(CFLAGS)' -l '$(LFLAGS) -lm' $^
