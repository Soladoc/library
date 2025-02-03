/// @file
/// @author Raphaël
/// @brief Tchatator413 Facade - Server implementation 
///
/// Uses Unix sockets.
///
/// @date 1/02/2025

#include <arpa/inet.h>
#include <errno.h>
#include <json-c.h>
#include <signal.h>
#include <stdbool.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/socket.h>
#include <sys/types.h>
#include <tchatator413/tchatator413.h>
#include <unistd.h>

#define SERVER_ADDR "127.0.0.1"

static inline void interpret_request(cfg_t *cfg, db_t *db, server_t *server, int fd) {
    put_log("interpreting request from fd %d\n", fd);

    char buf[BUFSIZ] = { 0 };
    ssize_t bytes_read = read(fd, buf, sizeof buf - 1);
    if (bytes_read > 0) buf[bytes_read] = '\0';

    json_object *obj_input = json_tokener_parse(buf);
    // if !obj_input : invalid JSON recieved

    put_log("received json input, interpreting request\n");

    json_object *obj_output = tchatator413_interpret(obj_input, cfg, db, server, NULL, NULL, NULL);
    json_object_put(obj_input);

    size_t len;
    ssize_t bytes_written;
    char const *output = json_object_to_json_string_length(obj_output, JSON_C_TO_STRING_PLAIN, &len);
    ++len; // include null terminator
    put_log("preparing to write %zu bytes of response\n", len);

    do {
        bytes_written = write(fd, output, len);
        if (-1 == bytes_written) errno_exit("write");
        len -= (size_t)bytes_written;
        output += bytes_written;
        put_log("wrote %zd bytes, %zu remaining\n", bytes_written, len);
    } while (len > 0);

    json_object_put(obj_output);
    put_log("request interpretation completed for fd %d\n", fd);
}

static int gs_sock = -1;

static void close_sock(int sig) {
    (void)sig;
    if (gs_sock == -1) return;
    put_log("closing socket %d\n", gs_sock);
    if (-1 == close(gs_sock)) perror("close"); // do not exit, we're already gonna do that by setting gs_sock to -1
    gs_sock = -1;
}

int tchatator413_run_server(cfg_t *cfg, db_t *db, server_t *server) {
    put_log("initializing server...\n");
    // Acquérir le socket
    gs_sock = socket(AF_INET, SOCK_STREAM, 0);
    if (-1 == gs_sock) errno_exit("socket");
    int sock_opt = 1;
    setsockopt(gs_sock, SOL_SOCKET, SO_REUSEADDR, &sock_opt, sizeof sock_opt);
    put_log("socket created with fd %d\n", gs_sock);

    // Programmer sa libération sur Ctrl+C
    if (SIG_ERR == signal(SIGINT, close_sock)) errno_exit("signal");
    if (SIG_ERR == signal(SIGTERM, close_sock)) errno_exit("signal");
    put_log("signal handlers installed\n");

    struct sockaddr_in server_addr = {
        .sin_addr.s_addr = inet_addr(SERVER_ADDR),
        .sin_family = AF_INET,
        .sin_port = htons(cfg_port(cfg)),
    };

    if (-1 == bind(gs_sock, (struct sockaddr *)&server_addr, sizeof(server_addr))) {
        perror("bind");
        return EXIT_FAILURE;
    }
    put_log("socket bound to address " SERVER_ADDR ":%hu\n", cfg_port(cfg));

    if (-1 == listen(gs_sock, cfg_backlog(cfg))) {
        perror("listen");
        return EXIT_FAILURE;
    }
    put_log("listening with backlog of %d\n", cfg_backlog(cfg));

    put_log("server started on " SERVER_ADDR " port %hu\n", cfg_port(cfg));

    struct sockaddr_in addr_connection;
    int size = sizeof addr_connection;

    while (true) {
        put_log("waiting for new connection...\n");
        int fd = accept(gs_sock, (struct sockaddr *)&addr_connection, (socklen_t *)&size);
        if (-1 == fd) {
            // If a signal interrupted accept().
            if (EINTR == errno) {
                // If the signal handler decided to exit
                if (gs_sock == -1) break;
                put_log("accept interrupted by signal, continuing...\n");
                continue;
            }
            errno_exit("accept");
        }
        put_log("accepted new connection from %s:%d with fd %d\n",
            inet_ntoa(addr_connection.sin_addr),
            ntohs(addr_connection.sin_port),
            fd);

        interpret_request(cfg, db, server, fd);

        put_log("closing connection fd %d\n", fd);
        close(fd);
    }

    put_log("server exiting...\n");

    return EX_OK;
}
