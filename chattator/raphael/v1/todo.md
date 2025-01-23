# todo

- [ ] elegant error handling
  - [ ] think of an approach that suits JSON, DB and other sources of errors

- [x] invoke build in bigpapoo/gcc-mariadb-pgsql:14.2-sae34
  - [x] make it work with make

- [ ] configuration
  - [ ] dynamic message string length
- [x] unit tests
  - [x] uuid

pgsql build:

```sh
apt install libjson-c-dev -y
gcc test-pgsql.c -o test-pgsql -I/usr/include/postgresql -L/usr/lib/x86_64-linux-gnu -lpq
```

pgsql:

```c
#include <stdio.h>
#include <stdlib.h>
#include <libpq-fe.h>

int main() {
    // Paramètres de connexion
    const char *conninfo = "host=localhost port=5432 dbname=testdb user=postgres password=password";

    // Connexion à PostgreSQL
    PGconn *conn = PQconnectdb(conninfo);

    // Vérification de la connexion
    if (PQstatus(conn) != CONNECTION_OK) {
        fprintf(stderr, "Erreur de connexion à la base de données : %s\n", PQerrorMessage(conn));
        PQfinish(conn);
        exit(EXIT_FAILURE);
    }

    printf("Connexion réussie à PostgreSQL !\n");

    // Requête SQL
    const char *query = "SELECT id, name FROM users";

    // Exécution de la requête
    PGresult *res = PQexec(conn, query);

    // Vérification des résultats
    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        fprintf(stderr, "Erreur lors de l'exécution de la requête : %s\n", PQerrorMessage(conn));
        PQclear(res);
        PQfinish(conn);
        exit(EXIT_FAILURE);
    }

    // Récupération et affichage des résultats
    int nrows = PQntuples(res);
    int nfields = PQnfields(res);

    printf("Résultats de la requête :\n");
    for (int i = 0; i < nrows; i++) {
        for (int j = 0; j < nfields; j++) {
            printf("%s ", PQgetvalue(res, i, j));
        }
        printf("\n");
    }

    // Libération des ressources et fermeture de la connexion
    PQclear(res);
    PQfinish(conn);

    printf("Connexion fermée.\n");
    return EXIT_SUCCESS;
```
