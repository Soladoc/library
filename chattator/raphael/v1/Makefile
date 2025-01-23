# compile project with generic rules
SOURCE	= src/action.c src/uuid.c
HEADER	= $(wildcard src/*.h)
CFLAGS	= -g -Og -Wall -Wextra -pedantic -fanalyzer -std=c17
LFLAGS	= -ljson-c -lm
CC	= gcc

.PHONY = all test clean

all: bin/act

test: bin/test/uuid
	bin/test/uuid

bin/act: src/act.c $(SOURCE) $(HEADER)
	mkdir -p bin
	$(CC) $(CFLAGS) src/act.c $(SOURCE) -o $@ $(LFLAGS)

bin/test/uuid: test/uuid.c $(SOURCE) $(HEADER) $(wildcard test/lib/*.h)
	mkdir -p bin/test
	$(CC) $(CFLAGS) test/uuid.c $(SOURCE) -o $@ $(LFLAGS)

clean:
	rm -r bin

