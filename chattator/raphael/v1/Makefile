# compile project with generic rules
SRC	=  $(wildcard src/*)
TEST_LIB = $(wildcard test/lib/*)

CFLAGS	= -g -Og -Wall -Wextra -pedantic -std=c23
LFLAGS	= -ljson-c -lm
CC	= gcc

.PHONY: all test clean

all: bin/act

test: bin/test/uuid
	bin/test/uuid

bin/act: act.c $(SRC)
	./docker-gcc -o $@ -c '$(CFLAGS) -fanalyzer' -l '$(LFLAGS)' $^

bin/test/uuid: test/uuid.c $(SRC) $(TEST_LIB)
	./docker-gcc -o $@ -c '$(CFLAGS)' -l '$(LFLAGS)' $^

clean:
	rm -r bin

