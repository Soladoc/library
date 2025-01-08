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
    int nbr_pong=1;
    char reponse[30];
    int resultat;
    int premier, deuxieme;
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
        if (strcmp(buffer,"HELLO\r\n")==0){
            snprintf(reponse, sizeof(reponse), "COUCOU LES GENS\r\n");
            write(cnx, reponse, strlen(reponse));
            memset(reponse, 0, sizeof(reponse));
        }
        else if (strcmp(buffer,"PING\r\n")==0){
            snprintf(reponse, sizeof(reponse), "PONG N°%d\r\n", nbr_pong);
            write(cnx, reponse, strlen(reponse));
            nbr_pong++;
            memset(reponse, 0, sizeof(reponse));
        }
        else if (sscanf(buffer, "%d*%d", &premier, &deuxieme) == 2) {
            resultat = premier * deuxieme;
            snprintf(reponse, sizeof(reponse), "Resultat de %d * %d = %d\r\n", premier, deuxieme, resultat);
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