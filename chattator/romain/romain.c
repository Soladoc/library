#include <arpa/inet.h>
#include <json-c/json.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>

#define SERVER_IP "127.0.0.1"
#define SERVER_PORT 4113
#define BUFFER_SIZE 1024

void send_request(const char *json_str);
static void login(const char *api_key, const char *password);
static void logout(int token);
static void send_message(int token, const char *dest, const char *content);
static void remove_message(int token, int msg_id);
static void inbox(int token);

int main(void) {
    int choix, token;
    char api_key[256], password[256], dest[256], content[1024];
    int msg_id;

    while (1) {
        printf("\nMenu:\n");
        printf("1. Connexion\n");
        printf("2. Déconnexion\n");
        printf("3. Envoyer un message\n");
        printf("4. Supprimer un message\n");
        printf("5. Voir la boîte de réception\n");
        printf("6. Quitter\n");
        printf("Choisissez une option: ");
        scanf("%d", &choix);
        getchar();

        switch (choix) {
        case 1:
            printf("Entrez votre clé API: ");
            fgets(api_key, 256, stdin);
            api_key[strcspn(api_key, "\n")] = 0;
            printf("Entrez votre mot de passe: ");
            fgets(password, 256, stdin);
            password[strcspn(password, "\n")] = 0;
            login(api_key, password);
            break;
        case 2:
            printf("Entrez votre token: ");
            scanf("%d", &token);
            logout(token);
            break;
        case 3:
            printf("Entrez votre token: ");
            scanf("%d", &token);
            getchar();
            printf("Destinataire: ");
            fgets(dest, 256, stdin);
            dest[strcspn(dest, "\n")] = 0;
            printf("Message: ");
            fgets(content, 1024, stdin);
            content[strcspn(content, "\n")] = 0;
            send_message(token, dest, content);
            break;
        case 4:
            printf("Entrez votre token: ");
            scanf("%d", &token);
            printf("Entrez l'ID du message: ");
            scanf("%d", &msg_id);
            remove_message(token, msg_id);
            break;
        case 5:
            printf("Entrez votre token: ");
            scanf("%d", &token);
            inbox(token);
            break;
        case 6:
            printf("Au revoir!\n");
            return 0;
        default:
            printf("Option invalide!\n");
        }
    }
}

void send_request(const char *json_str) {
    int sockfd;
    struct sockaddr_in server_addr;
    char buffer[BUFFER_SIZE];

    if ((sockfd = socket(AF_INET, SOCK_STREAM, 0)) < 0) {
        perror("Erreur de création du socket");
        exit(EXIT_FAILURE);
    }

    server_addr.sin_family = AF_INET;
    server_addr.sin_port = htons(SERVER_PORT);
    server_addr.sin_addr.s_addr = inet_addr(SERVER_IP);

    if (connect(sockfd, (struct sockaddr *)&server_addr, sizeof(server_addr)) < 0) {
        perror("Erreur de connexion au serveur");
        exit(EXIT_FAILURE);
    }

    send(sockfd, json_str, strlen(json_str), 0);

    int len = recv(sockfd, buffer, BUFFER_SIZE - 1, 0);
    if (len > 0) {
        buffer[len] = '\0';
        printf("Réponse du serveur: %s\n", buffer);
    }

    close(sockfd);
}

void login(const char *api_key, const char *password) {
    json_object *jobj = json_object_new_object();
    json_object *with_obj = json_object_new_object();

    json_object_object_add(with_obj, "api_key", json_object_new_string(api_key));
    json_object_object_add(with_obj, "password", json_object_new_string(password));
    json_object_object_add(jobj, "do", json_object_new_string("login"));
    json_object_object_add(jobj, "with", with_obj);

    send_request(json_object_to_json_string(jobj));
    json_object_put(jobj);
}

void logout(int token) {
    json_object *jobj = json_object_new_object();
    json_object_object_add(jobj, "do", json_object_new_string("logout"));
    json_object_object_add(jobj, "token", json_object_new_int(token));

    send_request(json_object_to_json_string(jobj));
    json_object_put(jobj);
}

void send_message(int token, const char *dest, const char *content) {
    json_object *jobj = json_object_new_object();
    json_object *with_obj = json_object_new_object();

    json_object_object_add(with_obj, "token", json_object_new_int(token));
    json_object_object_add(with_obj, "dest", json_object_new_string(dest));
    json_object_object_add(with_obj, "content", json_object_new_string(content));
    json_object_object_add(jobj, "do", json_object_new_string("send"));
    json_object_object_add(jobj, "with", with_obj);

    send_request(json_object_to_json_string(jobj));
    json_object_put(jobj);
}

void remove_message(int token, int msg_id) {
    json_object *jobj = json_object_new_object();
    json_object_object_add(jobj, "do", json_object_new_string("rm"));
    json_object_object_add(jobj, "token", json_object_new_int(token));
    json_object_object_add(jobj, "msg_id", json_object_new_int(msg_id));

    send_request(json_object_to_json_string(jobj));
    json_object_put(jobj);
}

void inbox(int token) {
    json_object *jobj = json_object_new_object();
    json_object_object_add(jobj, "do", json_object_new_string("inbox"));
    json_object_object_add(jobj, "token", json_object_new_int(token));

    send_request(json_object_to_json_string(jobj));
    json_object_put(jobj);
}
