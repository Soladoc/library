#include <sys/types.h>
#include <sys/socket.h>
#include <arpa/inet.h>
#include <netinet/in.h>
#include <unistd.h>
#include <string.h>
#include <stdio.h>
#include <stdlib.h>

int main(){
    int sock;
    int ret;
    struct sockaddr_in addr;
    int size;
    int cnx;
    struct sockaddr_in conn_addr;
    char buffer[1024];
    char reponse[30];
    sock = socket(AF_INET, SOCK_STREAM, 0);
    if (sock == -1) {
        perror("socket");
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
    size = sizeof(conn_addr);
    cnx = accept(sock, (struct sockaddr *)&conn_addr, (socklen_t *)&size);
    read(cnx, buffer, sizeof(buffer));
    while (strcmp(buffer,"BYE BYE\r\n")!=0){
        if (strcmp(buffer,"1\r\n")==0){
            snprintf(reponse, sizeof(reponse), "COUCOU LES GENS\r\n");
            write(cnx, reponse, strlen(reponse));
            memset(reponse, 0, sizeof(reponse));
        }
        else if (strcmp(buffer,"2\r\n")==0){
            snprintf(reponse, sizeof(reponse), "MESSAGE ENVOYE\r\n");
            write(cnx, reponse, strlen(reponse));
            memset(reponse, 0, sizeof(reponse));
        }
        else if (strcmp(buffer,"3\r\n")==0){
            snprintf(reponse, sizeof(reponse), "MESSAGE SUPPRIME\r\n");
            write(cnx, reponse, strlen(reponse));
            memset(reponse, 0, sizeof(reponse));
        }
        else if (strcmp(buffer,"4\r\n")==0){
            snprintf(reponse, sizeof(reponse), "MESSAGE MODIFIE\r\n");
            write(cnx, reponse, strlen(reponse));
            memset(reponse, 0, sizeof(reponse));
        }
        else if (strcmp(buffer,"5\r\n")==0){
            snprintf(reponse, sizeof(reponse), "UTILISATEUR BLOQUE\r\n");
            write(cnx, reponse, strlen(reponse));
            memset(reponse, 0, sizeof(reponse));
        }
        else if (strcmp(buffer,"6\r\n")==0){
            snprintf(reponse, sizeof(reponse), "UTILISATEUR DEBLOQUE\r\n");
            write(cnx, reponse, strlen(reponse));
            memset(reponse, 0, sizeof(reponse));
        }
        else if (strcmp(buffer,"7\r\n")==0){
            snprintf(reponse, sizeof(reponse), "RECUPERATION MESSAGES\r\n");
            write(cnx, reponse, strlen(reponse));
            memset(reponse, 0, sizeof(reponse));
        }
        else{
            snprintf(reponse, sizeof(reponse), "Commande inconnue\r\n");
            write(cnx, reponse, strlen(reponse));
            memset(reponse, 0, sizeof(reponse));
        }
        memset(buffer, 0, sizeof(buffer));
        read(cnx, buffer, sizeof(buffer));
    }
    return EXIT_SUCCESS;
}