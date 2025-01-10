#include <arpa/inet.h>
#include <netinet/in.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/select.h>
#include <sys/socket.h>
#include <sys/types.h>
#include <sys/wait.h>
#include <unistd.h>

int main() {
    int sock, cnx, option, ret, size, opt;
    struct sockaddr_in addr;
    struct sockaddr_in conn_addr;
    char reponse[30];
    ssize_t bytes_read;
    fd_set readfds;
    struct timeval timeout;

    opt = 1;
    sock = socket(AF_INET, SOCK_STREAM, 0);
    if (sock == -1) {
        perror("socket");
        _exit(EXIT_FAILURE);
    }

    if (setsockopt(sock, SOL_SOCKET, SO_REUSEADDR, &opt, sizeof(opt)) < 0) {
        perror("setsockopt");
        _exit(EXIT_FAILURE);
    }

    addr.sin_addr.s_addr = inet_addr("127.0.0.1");
    addr.sin_family = AF_INET;
    addr.sin_port = htons(8080);

    ret = bind(sock, (struct sockaddr *)&addr, sizeof(addr));
    if (ret == -1) {
        perror("bind");
        _exit(EXIT_FAILURE);
    }

    ret = listen(sock, 1);
    if (ret == -1) {
        perror("listen");
        _exit(EXIT_FAILURE);
    }

    printf("Serveur démarré.\n");

    size = sizeof(conn_addr);
    cnx = accept(sock, (struct sockaddr *)&conn_addr, (socklen_t *)&size);
    if (cnx == -1) {
        perror("accept");
        _exit(EXIT_FAILURE);
    }

    FD_ZERO(&readfds);
    FD_SET(cnx, &readfds);
    timeout.tv_sec = 10;
    timeout.tv_usec = 0;

    while (1) {
        ret = select(cnx + 1, &readfds, NULL, NULL, &timeout);

        if (ret == -1) {
            perror("select()");
            close(cnx);
            break;
        } else if (ret == 0) {
            printf("Timeout waiting for data.\n");
            // Reset the timeout for the next round of select
            timeout.tv_sec = 10;
            timeout.tv_usec = 0;
            FD_SET(cnx, &readfds);  // Ensure we keep monitoring the connection
        } else {
            // Socket is ready to read
            bytes_read = read(cnx, &option, sizeof(option));

            if (bytes_read > 0) {
                printf("Received option: %d\n", option);
                switch (option) {
                    case 1:
                        snprintf(reponse, sizeof(reponse), "AFFICHAGE MESSAGES\r\n");
                        break;
                    case 2:
                        snprintf(reponse, sizeof(reponse), "MESSAGE ENVOYE\r\n");
                        break;
                    case 3:
                        snprintf(reponse, sizeof(reponse), "MESSAGE SUPPRIME\r\n");
                        break;
                    case 4:
                        snprintf(reponse, sizeof(reponse), "MESSAGE MODIFIE\r\n");
                        break;
                    case 5:
                        snprintf(reponse, sizeof(reponse), "UTILISATEUR BLOQUE\r\n");
                        break;
                    case 6:
                        snprintf(reponse, sizeof(reponse), "UTILISATEUR DEBLOQUE\r\n");
                        break;
                    case 7:
                        snprintf(reponse, sizeof(reponse), "RECUPERATION MESSAGES\r\n");
                        break;
                    case 8:
                        snprintf(reponse, sizeof(reponse), "Au revoir.\r\n");
                        break;
                    default:
                        snprintf(reponse, sizeof(reponse), "Commande inconnue\r\n");
                        break;
                }

                write(cnx, reponse, strlen(reponse));

                if (option == 8) {
                    close(cnx);
                    break;
                }
            } else {
                printf("Error reading option, bytes_read: %zd\n", bytes_read);
                close(cnx);
                break;
            }
        }
    }

    close(sock);
    printf("Le serveur s'arrête.\r\n");
    return EXIT_SUCCESS;
}