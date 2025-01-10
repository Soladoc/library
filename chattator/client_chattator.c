#include <arpa/inet.h>
#include <netinet/in.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/socket.h>
#include <sys/types.h>
#include <unistd.h>

int main() {
    int sock;
    int ret;
    struct sockaddr_in addr;
    char buffer[10000];
    char message[1000];
    int option;
    ssize_t bytes_read;

    sock = socket(AF_INET, SOCK_STREAM, 0);
    addr.sin_addr.s_addr = inet_addr("127.0.0.1");
    addr.sin_family = AF_INET;
    addr.sin_port = htons(8080);

    ret = connect(sock, (struct sockaddr *)&addr, sizeof(addr));
    if (ret == -1) {
        printf("Connexion impossible.(Code 404)\n");
        perror("connect");
        _exit(EXIT_FAILURE);
    } else {
        printf("Connexion établie avec le serveur.(Code 200)\n");
        printf("Bienvenue dans votre espace de chat.\n");
    }

    option = 0;
    while (option != 8) {
        printf("Que voulez-vous faire ? \n");
        printf("Tapez 1 pour voir vos messages\n");
        printf("Tapez 2 pour envoyer un message\n");
        printf("Tapez 3 pour supprimer un message\n");
        printf("Tapez 4 pour modifier un message\n");
        printf("Tapez 5 pour bloquer un utilisateur\n");
        printf("Tapez 6 pour débloquer un utilisateur\n");
        printf("Tapez 7 pour récupérer vos messages dans un fichier JSON\n");
        printf("Tapez 8 pour quitter\n");
        scanf("%d", &option);
        getchar();

        write(sock, &option, sizeof(option));
        printf("Sent option: %d\n", option);
        fflush(stdout);

        if (option == 1 || option == 3 || option == 4 || option == 5 || option == 6 || option == 7) {
            bytes_read = read(sock, buffer, sizeof(buffer) - 1);
            if (bytes_read > 0) {
                buffer[bytes_read] = '\0';
                printf("%s", buffer);
            }
        } else if (option == 2) {
            bytes_read = read(sock, buffer, sizeof(buffer) - 1);
            if (bytes_read > 0) {
                buffer[bytes_read] = '\0';
                printf("%s", buffer);  // Print server's response
            }
            fgets(message, sizeof(message), stdin);
            message[strcspn(message, "\n")] = 0;
            write(sock, message, strlen(message));
            fflush(stdout);
            bytes_read = read(sock, buffer, sizeof(buffer) - 1);
            if (bytes_read > 0) {
                buffer[bytes_read] = '\0';
                printf("%s", buffer);
            }
        }

    }

    close(sock);
    return EXIT_SUCCESS;
}