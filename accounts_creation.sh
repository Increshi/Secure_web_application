#! /bin/bash

CONTAINER_NAME="mysql_db"
CSV_FILE="hello.csv"
SQL_CONTAINER_PATH="/tmp/$SQL_SCRIPT"
CSV_CONTAINER_PATH="/var/lib/mysql-files/$CSV_FILE"

IMPORT_SQL_COMMAND="LOAD DATA INFILE \\\"$CSV_CONTAINER_PATH\\\" INTO TABLE users FIELDS TERMINATED BY \\\",\\\" ENCLOSED BY \\\"\\\\\\\"\\\" LINES TERMINATED BY \\\"\\\\n\\\" IGNORE 1 ROWS (name, username, email, password);"



DB_NAME="app_db"
DB_USER="root"
DB_PASSWORD="Rushi@123"


EXEC_COMMAND="mysql -u $DB_USER -p$DB_PASSWORD $DB_NAME -e \"$IMPORT_SQL_COMMAND\""
echo $EXEC_COMMAND

if [ "$(docker ps -q -f name=$CONTAINER_NAME)" ]; then
    echo "Database container is running."

    echo "Copying CSV file to container..."
    docker cp $CSV_FILE $CONTAINER_NAME:$CSV_CONTAINER_PATH
    echo "CSV file to copied container..."

    docker exec -i $CONTAINER_NAME /bin/sh -c \'mysql -u $DB_USER -p$DB_PASSWORD $DB_NAME -e \"$IMPORT_SQL_COMMAND\"\'

else 
    echo "DB Container failed"

fi

# docker exec -i mysql_db /bin/sh -c 'mysql -u root -pRushi@123 app_db -e "LOAD DATA INFILE \"/var/lib/mysql-files/hello.csv\" INTO TABLE users FIELDS TERMINATED BY \",\" ENCLOSED BY \"\\\"\" LINES TERMINATED BY \"\\n\" IGNORE 1 ROWS (name, username, email, password);"'
# docker exec -i mysql_db /bin/sh -c 'mysql -u root -pRushi@123 app_db -e "LOAD DATA INFILE \"/var/lib/mysql-files/hello.csv\" INTO TABLE users FIELDS TERMINATED BY \",\" ENCLOSED BY \"\\\"\" LINES TERMINATED BY \"\\n\" IGNORE 1 ROWS (name, username, email, password);"'   
   
   
   
   
   
    # id INT AUTO_INCREMENT PRIMARY KEY,
    # name VARCHAR(50) NOT NULL,
    # username VARCHAR(50) UNIQUE NOT NULL,
    # email VARCHAR(100) UNIQUE NOT NULL,
    # password VARCHAR(255) NOT NULL,
    # balance DECIMAL(10,2) DEFAULT 100,
    # profile_image VARCHAR(255) DEFAULT NULL,
    # biography TEXT DEFAULT NULL,
    # created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP


# LOAD DATA INFILE '/tmp/hello.csv' INTO TABLE users 
#                 FIELDS TERMINATED BY ',' 
#                 ENCLOSED BY '\"' 
#                 LINES TERMINATED BY '\\n' 
#                 IGNORE 1 ROWS (name, username, email, password);