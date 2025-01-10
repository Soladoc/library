#include <sys/types.h>
#include <sys/socket.h>
#include <arpa/inet.h>
#include <netinet/in.h>
#include <unistd.h>
#include <string.h>
#include <stdio.h>
#include <stdlib.h>
#include <sys/wait.h>
#include <sys/select.h>

int main(){
    int sock,cnx,option,ret,size,opt;
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
    FD_ZERO(&readfds);
    FD_SET(cnx, &readfds);
    timeout.tv_sec = 5;
    timeout.tv_usec = 0;
    while(1) {
        ret = select(cnx + 1, &readfds, NULL, NULL, &timeout);
        if (ret == -1) {
    perror("select()");
} else if (ret == 0) {
    printf("Timeout waiting for data.\n");
} else {
    // The socket is ready to read, proceed with read()
    bytes_read = read(cnx, &option, sizeof(option));
    if (bytes_read > 0) {
        printf("Received option: %d\n", option);
    } else {
        printf("Error reading option, bytes_read: %zd\n", bytes_read);
    }
}

        if (bytes_read <= 0) {
            close(cnx);
            break;
        }

        switch(option) {
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
            default:
                snprintf(reponse, sizeof(reponse), "Commande inconnue\r\n");
        }
        write(cnx, reponse, strlen(reponse));
        if (option==8){
            write(cnx, reponse, strlen(reponse));
            close(cnx);
            break;
        }
    }
    close(sock);
    printf("Le serveur s'arrête.\r\n");
    return EXIT_SUCCESS;
}