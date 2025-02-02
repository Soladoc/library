#include <arpa/inet.h>
#include <json-c/json.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>

#define SERVER_IP "127.0.0.1"
#define SERVER_PORT 4113
#define BUFFER_SIZE 1024

json_object *send_request(json_object *obj);
static void login(const char *api_key, const char *password);
static void logout(void);
static void send_message(const char *dest, const char *content);
static void remove_message(int msg_id);
static void inbox(void);

static void put_state(void);

typedef long token_t;

static struct {
    enum {
        state_unconnected,
        state_connected,
    } tag;
    union {
        struct {
            char username[256];
            token_t token;
        } connected;
    } info;
} gs_state;

static void put_state(void) {
    switch (gs_state.tag) {
    case state_connected:
        printf("Statut: Connecté : %s (token %ld)\n", gs_state.info.connected.username, gs_state.info.connected.token);
        break;
    case state_unconnected:
        puts("Statut: Non connecté");
        break;
    default: break;
    }
}

int main(void) {
    int choix;
    char api_key[256], password[256], dest[256], content[1024];
    int msg_id;

    while (!feof(stdin)) {
        put_state();
        printf("\nMenu:\n");
        if (gs_state.tag == state_unconnected) printf("1. Connexion\n");
        if (gs_state.tag == state_connected) printf("2. Déconnexion\n");
        if (gs_state.tag == state_connected) printf("3. Envoyer un message\n");
        if (gs_state.tag == state_connected) printf("4. Supprimer un message\n");
        if (gs_state.tag == state_connected) printf("5. Voir la boîte de réception\n");
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
            logout();
            break;
        case 3:
            printf("Destinataire (pseudo de membre ou dénomination de professionnel): ");
            fgets(dest, 256, stdin);
            dest[strcspn(dest, "\n")] = 0;
            printf("Message: ");
            fgets(content, 1024, stdin);
            content[strcspn(content, "\n")] = 0;
            send_message(dest, content);
            break;
        case 4:
            printf("Entrez l'ID du message: ");
            scanf("%d", &msg_id);
            remove_message(msg_id);
            break;
        case 5:
            inbox();
            break;
        case 6:
            printf("Au revoir!\n");
            return 0;
        default:
            printf("Option invalide!\n");
        }
    }
}

json_object *send_request(json_object *obj) {
    int sockfd;
    struct sockaddr_in server_addr;

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

    const char *json_str = json_object_to_json_string_ext(obj, JSON_C_TO_STRING_PLAIN);

    printf("Envoi de %s\n", json_str);

    send(sockfd, json_str, strlen(json_str), 0);

    json_object *rep = json_object_from_fd(sockfd);
    printf("Réponse: %s\n", json_object_to_json_string(rep));

    json_object *action = json_object_array_get_idx(rep, 0);

    json_object *body;

    if (json_object_object_get_ex(action, "body", &body)) {
    } else if (json_object_object_get_ex(action, "error", &body)) {
        body = NULL;
    } else {
        printf("Bizarre...\n"); // FIXME
        body = NULL;
    }

    close(sockfd);

    json_object_put(rep);
    return body;
}

void login(const char *api_key, const char *password) {
    json_object *jobj = json_object_new_object();
    json_object *with_obj = json_object_new_object();

    json_object_object_add(with_obj, "api_key", json_object_new_string(api_key));
    json_object_object_add(with_obj, "password", json_object_new_string(password));
    json_object_object_add(jobj, "do", json_object_new_string("login"));
    json_object_object_add(jobj, "with", with_obj);

    json_object *obj_rep = send_request(jobj);
    json_object_put(jobj);
    if (!obj_rep) return;

    gs_state.tag = state_connected;
    gs_state.info.connected.token = json_object_get_int64(json_object_object_get(obj_rep, "token"));
}

void logout() {
    json_object *jobj = json_object_new_object();
    json_object_object_add(jobj, "do", json_object_new_string("logout"));
    json_object_object_add(jobj, "token", json_object_new_int64(gs_state.info.connected.token));

    json_object *obj_rep = send_request(jobj);
    json_object_put(jobj);
    if (!obj_rep) return;

    gs_state.tag = state_unconnected;

    json_object_put(obj_rep);
}

void send_message(const char *dest, const char *content) {
    json_object *jobj = json_object_new_object();
    json_object *with_obj = json_object_new_object();

    json_object_object_add(with_obj, "token", json_object_new_int64(gs_state.info.connected.token));
    json_object_object_add(with_obj, "dest", json_object_new_string(dest));
    json_object_object_add(with_obj, "content", json_object_new_string(content));
    json_object_object_add(jobj, "do", json_object_new_string("send"));
    json_object_object_add(jobj, "with", with_obj);

    send_request(jobj);
    json_object_put(jobj);
}

void remove_message(int msg_id) {
    json_object *jobj = json_object_new_object();
    json_object_object_add(jobj, "do", json_object_new_string("rm"));
    json_object_object_add(jobj, "token", json_object_new_int64(gs_state.info.connected.token));
    json_object_object_add(jobj, "msg_id", json_object_new_int(msg_id));

    send_request(jobj);
    json_object_put(jobj);
}

void inbox(void) {
    json_object *jobj = json_object_new_object();
    json_object_object_add(jobj, "do", json_object_new_string("inbox"));
    json_object *with = json_object_new_object();
    json_object_object_add(jobj, "with", with);
    json_object_object_add(with, "token", json_object_new_int64(gs_state.info.connected.token));

    json_object *rep = send_request((jobj));
    json_object_put(jobj);
    if (!rep) return;

    int length = json_object_array_length(rep);
    for (int i = 0; i < length; ++i) {
        json_object *obj = json_object_array_get_idx(rep, i);
        printf("%d, envoyé le %ld, de %d: %s \n",
            json_object_get_int(json_object_object_get(obj, "msg_id")),
            json_object_get_int64(json_object_object_get(obj, "sent_at")),
            json_object_get_int(json_object_object_get(obj, "sender")),
            json_object_get_string(json_object_object_get(obj, "content")));
    }

    json_object_put(rep);
}
