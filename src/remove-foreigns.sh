#!/bin/bash

schema="cxknt_production"
user="root"
pass="hft7KLdn89Jws"
host="dev-adt-bck.cmrppsxji0j2.eu-west-1.rds.amazonaws.com"
mysql="mysql -h $host -p$pass -u $user $schema"

fksQueryResult=`echo \
"SELECT TABLE_NAME, CONSTRAINT_NAME" \
"FROM information_schema.TABLE_CONSTRAINTS" \
"WHERE CONSTRAINT_TYPE='FOREIGN KEY' AND TABLE_SCHEMA='$schema';"\
| $mysql`
declare -a fks
fks=($fksQueryResult)

for((i=2;i<${#fks[@]};i+=2)); do
  iinc=i+1
  tableName=${fks[$i]}
  keyName=${fks[$iinc]}
  echo "Dropping $tableName : $keyName"
  echo "ALTER TABLE $tableName"\
       "DROP FOREIGN KEY $keyName;"\
    | $mysql 
done