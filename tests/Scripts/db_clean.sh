#!/bin/bash

DATABASES="$(mysql -u root -pJk48695715 -Bse 'show databases')"
REGEX="^([a-z0-9]{8}\-?)([a-z0-9]{4}\-?){3}([a-z0-9]{12})"

for db in ${DATABASES}; do
    if [[ ${db} =~ ${REGEX} ]]; then
        echo "Deleting database ${db}"
        echo Y | mysqladmin -u root -pJk48695715 drop ${db}
    fi
done

mysql -u root -pJk48695715 -D "mysql" -NBe "select User, Host from user" | while read -r user host;
do
    if [[ ${user} =~ ${REGEX} ]]; then
            echo "Deleting user '${user}'@'${host}'"
            mysql -u root -pJk48695715 -Bse "drop user '${user}'@'${host}'"
    fi
done

