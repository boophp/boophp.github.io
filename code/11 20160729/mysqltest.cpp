//#include <my_global.h>
#include <mysql.h>
#include<stdio.h>
#include<stdlib.h>

int main(int argc, char **argv)
{
  MYSQL *conn;
  MYSQL_RES *result;
  MYSQL_ROW row;
  int num_fields;
  int i;

  conn = mysql_init(NULL);
  mysql_real_connect(conn, "101.251.196.91", "quanzhan", "5jsx2qs", "mid_config", 0, NULL, 0);

  printf("MySQL client version: %s\n", mysql_get_client_info());
  mysql_query(conn, "SELECT * FROM qz_server_setting");
  result = mysql_store_result(conn);

  num_fields = mysql_num_fields(result);
  MYSQL_FIELD *fields = mysql_fetch_fields(result);

  while ((row = mysql_fetch_row(result)))
  {
      for(i = 0; i < num_fields; i++)
      {
          printf("%s:%s ", fields[i].name, row[i] ? row[i] : "NULL");
      }
      printf("\n");
  }

  mysql_free_result(result);
  mysql_close(conn);

}
