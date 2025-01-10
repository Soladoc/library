#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <unistd.h>
#include <string.h>
#include <stdio.h>
#include <stdlib.h>
#include <sys/wait.h>

int main(){
    int sock;
    int ret;
    struct sockaddr_in addr;
    char buffer[10000];
    char message[1000];
    int option;
    int num_message;
    sock = socket(AF_INET, SOCK_STREAM, 0);
    addr.sin_addr.s_addr = inet_addr("127.0.0.1");
    addr.sin_family = AF_INET;
    addr.sin_port = htons(8080);
    ret = connect(sock, (struct sockaddr *)&addr, sizeof(addr));
    if (ret == -1) {
        printf("Connexion impossible.(Code 404)\n");
        perror("connect");
        _exit(EXIT_FAILURE);
    }
    else {
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
        if (option == 1) {
            write(sock, &option, sizeof(option));
        }
        else if (option == 2) {
            printf("Entrez votre message :\n");
            fgets(message, sizeof(message), stdin);
            message[strcspn(message, "\n")] = 0;
            write(sock, &option, sizeof(option));
            write(sock, message, strlen(message));
        }
        else if (option == 3) {
            printf("Entrez le numéro du message à supprimer :\n");
            scanf("%d", &num_message);
            getchar();
            write(sock, &option, sizeof(option));
            write(sock, &num_message, sizeof(num_message));
        }
        else if (option == 4) {
            printf("Entrez le numéro du message à modifier :\n");
            scanf("%d", &num_message);
            getchar();
            printf("Entrez votre message :\n");
            fgets(message, sizeof(message), stdin);
            message[strcspn(message, "\n")] = 0;
            write(sock, &option, sizeof(option));
            write(sock, &num_message, sizeof(num_message));
            write(sock, message, strlen(message));
        }
        else if (option == 5) {
            printf("Entrez le nom de l'utilisateur à bloquer :\n");
            fgets(message, sizeof(message), stdin);
            message[strcspn(message, "\n")] = 0;
            write(sock, &option, sizeof(option));
            write(sock, message, strlen(message));
        }
        else if (option == 6) {
            printf("Entrez le nom de l'utilisateur à débloquer :\n");
            fgets(message, sizeof(message), stdin);
            message[strcspn(message, "\n")] = 0;
            write(sock, &option, sizeof(option));
            write(sock, message, strlen(message));
        }
        else if (option == 7) {
            write(sock, &option, sizeof(option));
        }
        else if (option == 8) {
            snprintf(message, sizeof(message), "BYE BYE\r\n");
            write(sock, message, strlen(message));
            memset(buffer, 0, sizeof(buffer));
            read(sock, buffer, sizeof(buffer));
            printf("%s\n", buffer);
            break;
        }
        else {
            printf("Commande inconnue. Veuillez entrer un nombre entre 1 et 8.\n");
        }

        memset(buffer, 0, sizeof(buffer));
        read(sock, buffer, sizeof(buffer));
        printf("%s\n", buffer);
    }
    close(sock);
    return EXIT_SUCCESS;
}