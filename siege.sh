#!/bin/bash
HOST=10.126.0.2
PORT=8000
TEST_TIME=10s
SLEEP_TIME=10
ITERATION=2
CONCURENCY=(200)
AUTH_METHOD=basic
USERNAME=perftest
PASSWORD=perftest
init_db() {
  PGPASSWORD=postgres psql -h $HOST -U postgres lumen < dump-sql.sql
}
drop_table_db() {
  PGPASSWORD=postgres psql -h $HOST -U postgres lumen -c "DROP TABLE user_person, hardware, node, sensor, channel;"
}
rollback_db() {
  drop_table_db  
  init_db
}
perftest() {
  local method=$1
  local endpoint=$2
  local data=$3
  local do_rollback=$4
  for concurrent in "${CONCURENCY[@]}"
  do
    for i in $(seq 1 $ITERATION)
    do
      echo "[ $method $endpoint $concurrent][$i]"
      if [ $method == "GET" ] 
      then
        siege -t$TEST_TIME -c$concurrent $HOST:$PORT$endpoint --header="$HEADER"
      # elif [ $method == "DELETE" ]
      # then
      #   siege -X DELETE -t$TEST_TIME -c$concurrent $HOST:$PORT$endpoint --header="$HEADER"
      elif [ $method == "PUT" ] || [ $method == "POST" ]
      then
        siege -t$TEST_TIME -c$concurrent "$HOST:$PORT$endpoint $method $data" --header="$HEADER" --content-type "application/json"
      fi
      sleep $SLEEP_TIME
      if $do_rollback
      then
        rollback_db > /dev/null
      fi
    done
  done
}
auth() {
  if [ $AUTH_METHOD == "jwt" ]
  then
    HEADER="Authorization: Bearer $(http $HOST:$PORT/user/login username=perftest password=perftest | jq -r '.token')"
  elif [ $AUTH_METHOD == "basic" ]
  then
    HEADER="Authorization: Basic $(echo -n $USERNAME:$PASSWORD | base64)"
  fi
}
rollback_db
auth
echo $HEADER
## perftest METHOD ENDPOINT DATA DO_ROLLBACK
# perftest "POST" "/channel" "{\"value\": 1.33, \"id_sensor\": 1}" true
perftest "GET" "/node" "" false
# perftest "GET" "/node/1" ""  false
# perftest "PUT" "/node/1" "{ \"name\":\"test\",\"location\":\"test\",\"id_hardware\":1 }" true
# perftest "DELETE" "/node/1" "" true