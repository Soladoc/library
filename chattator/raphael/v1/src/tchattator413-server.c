#include <arpa/inet.h>
#include <errno.h>
#include <json-c/json.h>
#include <signal.h>
#include <stdbool.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/socket.h>
#include <sys/types.h>
#include <tchattator413/tchattator413.h>
#include <unistd.h>

#define SERVER_ADDR "127.0.0.1"

static inline void interpret_request(cfg_t *cfg, db_t *db, server_t *server, int fd) {
    json_object *obj_input = json_object_from_fd(fd);
    json_object *obj_output = tchattator413_interpret(obj_input, cfg, db, server, NULL, NULL, NULL);
    json_object_put(obj_input);

    size_t len;
    ssize_t bytes_written;
    char const *output = json_object_to_json_string_length(obj_output, JSON_C_TO_STRING_PLAIN, &len);
    ++len; // include null terminator

    do {
        bytes_written = write(fd, output, len);
        if (-1 == bytes_written) errno_exit("write");
        len -= bytes_written;
        output += bytes_written;
    } while (len > 0);

    json_object_put(obj_output);
}

static int gs_sock = -1;

static void close_sock(int sig) {
    (void)sig;
    if (gs_sock == -1) return;
    put_log("closing socket %d\n", gs_sock);
    if (-1 == close(gs_sock)) perror("close"); // do not exit, we're already gonna do that by setting gs_sock to -1
    gs_sock = -1;
}

int tchattator413_run_server(cfg_t *cfg, db_t *db, server_t *server) {
    // Acquérir le socket
    gs_sock = socket(AF_INET, SOCK_STREAM, 0);
    if (-1 == gs_sock) errno_exit("socket");
    int sock_opt = 1;
    setsockopt(gs_sock, SOL_SOCKET, SO_REUSEADDR, &sock_opt, sizeof sock_opt);

    // Programmer sa libération sur Ctrl+C
    if (SIG_ERR == signal(SIGINT, close_sock)) errno_exit("signal");
    if (SIG_ERR == signal(SIGTERM, close_sock)) errno_exit("signal");

    struct sockaddr_in server_addr = {
        .sin_addr.s_addr = inet_addr(SERVER_ADDR),
        .sin_family = AF_INET,
        .sin_port = htons(cfg_port(cfg)),
    };

    if (-1 == bind(gs_sock, (struct sockaddr *)&server_addr, sizeof(server_addr))) {
        perror("bind");
        return EXIT_FAILURE;
    }

    if (-1 == listen(gs_sock, cfg_backlog(cfg))) {
        perror("listen");
        return EXIT_FAILURE;
    }

    put_log("server started on " SERVER_ADDR " port %hu\n", cfg_port(cfg));

    struct sockaddr_in addr_connection;
    int size = sizeof addr_connection;

    while (true) {
        int fd = accept(gs_sock, (struct sockaddr *)&addr_connection, (socklen_t *)&size);
        if (-1 == fd) {
            // If a signal interrupted accept().
            if (EINTR == errno) {
                // If the signal handler decided to exit
                if (gs_sock == -1) break;
                continue;
            }
            errno_exit("accept");
        }

        interpret_request(cfg, db, server, fd);

        close(fd);
    }

    put_log("server exiting...\n");

    return EX_OK;
}
