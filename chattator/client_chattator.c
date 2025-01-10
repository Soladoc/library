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
    int num_message;
    ssize_t bytes_read;

    // Create socket
    sock = socket(AF_INET, SOCK_STREAM, 0);
    if (sock == -1) {
        printf("Erreur lors de la création du socket.\n");
        _exit(EXIT_FAILURE);
    }

    // Set server address
    addr.sin_addr.s_addr = inet_addr("127.0.0.1");
    addr.sin_family = AF_INET;
    addr.sin_port = htons(8080);

    // Connect to the server
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
        // Display menu and prompt for option
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
        getchar();  // To consume the newline left by scanf

        // Send the option to the server
        write(sock, &option, sizeof(option));
        printf("Sent option: %d\n", option);
        fflush(stdout);

        // Wait for a response from the server
        memset(buffer, 0, sizeof(buffer));
        bytes_read = read(sock, buffer, sizeof(buffer) - 1);
        if (bytes_read > 0) {
            buffer[bytes_read] = '\0';
            printf("%s\n", buffer);
        }

        // Handle the logic for specific options
        if (option == 2) {  // If option 2 is selected (send a message)
            fgets(message, sizeof(message), stdin);
            message[strcspn(message, "\n")] = 0;  // Remove newline character
            write(sock, message, strlen(message));
            fflush(stdout);
        } else if (option == 3) {  // If option 3 is selected (delete a message)
            printf("Entrez le numéro du message à supprimer :\n");
            scanf("%d", &num_message);
            getchar();  // Consume newline
            write(sock, &num_message, sizeof(num_message));
            fflush(stdout);
        } else if (option == 4) {  // If option 4 is selected (modify a message)
            printf("Entrez le numéro du message à modifier :\n");
            scanf("%d", &num_message);
            getchar();  // Consume newline
            printf("Entrez votre nouveau message :\n");
            fgets(message, sizeof(message), stdin);
            message[strcspn(message, "\n")] = 0;  // Remove newline character
            write(sock, &num_message, sizeof(num_message));
            write(sock, message, strlen(message));
            fflush(stdout);
        } else if (option == 5) {  // If option 5 is selected (block a user)
            printf("Entrez le nom de l'utilisateur à bloquer :\n");
            fgets(message, sizeof(message), stdin);
            message[strcspn(message, "\n")] = 0;  // Remove newline character
            write(sock, message, strlen(message));
            fflush(stdout);
        } else if (option == 6) {  // If option 6 is selected (unblock a user)
            printf("Entrez le nom de l'utilisateur à débloquer :\n");
            fgets(message, sizeof(message), stdin);
            message[strcspn(message, "\n")] = 0;  // Remove newline character
            write(sock, message, strlen(message));
            fflush(stdout);
        } else if (option == 7) {  // If option 7 is selected (retrieve messages in JSON)
            printf("Entrez le nom de l'utilisateur pour récupérer les messages :\n");
            fgets(message, sizeof(message), stdin);
            message[strcspn(message, "\n")] = 0;  // Remove newline character
            write(sock, message, strlen(message));
            fflush(stdout);
        }
    }

    // After option 8 (quit), read the server response and then exit
    memset(buffer, 0, sizeof(buffer));
    bytes_read = read(sock, buffer, sizeof(buffer) - 1);
    if (bytes_read > 0) {
        buffer[bytes_read] = '\0';
        printf("%s\n", buffer);
    }

    close(sock);  // Close the socket
    return EXIT_SUCCESS;
}
