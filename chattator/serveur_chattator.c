#include <arpa/inet.h>
#include <netinet/in.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/select.h>
#include <sys/socket.h>
#include <sys/types.h>
#include <unistd.h>

int main() {
    int sock, cnx, option, ret, size, opt;
    struct sockaddr_in addr;
    struct sockaddr_in conn_addr;
    char reponse[1019];
    char message[1000];
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

    while (1) {
        FD_ZERO(&readfds);
        FD_SET(cnx, &readfds);
        timeout.tv_sec = 10;
        timeout.tv_usec = 0;

        // Wait for client input (blocking)
        ret = select(cnx + 1, &readfds, NULL, NULL, &timeout);

        if (ret == -1) {
            perror("select()");
        } else if (ret == 0) {
            // Timeout - no data from client
            printf("Timeout waiting for data.\n");
        } else if (FD_ISSET(cnx, &readfds)) {
            // Client sent data, process it
            bytes_read = read(cnx, &option, sizeof(option));

            if (bytes_read > 0) {
                printf("Received option: %d\n", option);
            } else {
                printf("Error reading option, bytes_read: %zd\n", bytes_read);
                break;
            }

            // Handle options based on client input
            switch (option) {
                case 1:  // "AFFICHAGE MESSAGES"
                    snprintf(reponse, sizeof(reponse), "AFFICHAGE MESSAGES\r\n");
                    break;
                case 2:  // "MESSAGE ENVOYE"
                    snprintf(reponse, sizeof(reponse), "Entrez votre message :\r\n");
                    write(cnx, reponse, strlen(reponse));  // Prompt client for message
                    
                    // Wait for the actual message from the client
                    bytes_read = read(cnx, message, sizeof(message) - 1);
                    if (bytes_read > 0) {
                        message[bytes_read] = '\0';  // Null-terminate the message
                        printf("Message reçu : %s\n", message);
                        snprintf(reponse, sizeof(reponse), "MESSAGE ENVOYE: %s\r\n", message);
                    } else {
                        snprintf(reponse, sizeof(reponse), "Erreur lors de la réception du message\r\n");
                    }
                    break;
                case 3:  // "MESSAGE SUPPRIME"
                    snprintf(reponse, sizeof(reponse), "MESSAGE SUPPRIME\r\n");
                    break;
                case 4:  // "MESSAGE MODIFIE"
                    snprintf(reponse, sizeof(reponse), "MESSAGE MODIFIE\r\n");
                    break;
                case 5:  // "UTILISATEUR BLOQUE"
                    snprintf(reponse, sizeof(reponse), "UTILISATEUR BLOQUE\r\n");
                    break;
                case 6:  // "UTILISATEUR DEBLOQUE"
                    snprintf(reponse, sizeof(reponse), "UTILISATEUR DEBLOQUE\r\n");
                    break;
                case 7:  // "RECUPERATION MESSAGES"
                    snprintf(reponse, sizeof(reponse), "RECUPERATION MESSAGES\r\n");
                    break;
                case 8:  // "Au revoir"
                    snprintf(reponse, sizeof(reponse), "Au revoir.");
                    break;
                default:
                    snprintf(reponse, sizeof(reponse), "Commande inconnue\r\n");
                    break;
            }

            // Send the response back to the client
            write(cnx, reponse, strlen(reponse));

            // If option 8 (Exit) is selected, break the loop and close the connection
            if (option == 8) {
                close(cnx);
                break;
            }
        }
    }

    close(sock);
    printf("Le serveur s'arrête.\r\n");
    return EXIT_SUCCESS;
}
