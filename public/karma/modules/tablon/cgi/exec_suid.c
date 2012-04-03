#include <unistd.h>
#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <libgen.h>

int main(int argc,char **argv)
{
	char *puntero;
	char comando[1024];
	char comando_real[1024];
	int retorno;
		uid_t miuid;

   miuid = geteuid();
   setreuid(miuid,miuid);
	
	
	char *fichero = getenv("PHP_CGI_FILE");
	
	puntero = strstr(fichero,";");
	if (puntero != NULL ) {
		exit(99);	
	}
	
	puntero = strstr(fichero,"|");
	if (puntero != NULL ) {
		exit(99);	
	}
	
	puntero = strstr(fichero,".");
	if (puntero != NULL ) {
		exit(99);	
	}
	
	

	sprintf(comando,"./%s.karma_cgi.php",fichero);
	sprintf(comando_real,"%s/%s.karma_cgi.php",dirname(argv[0]),fichero);
	
	retorno=execl(comando_real,comando_real,NULL);
	
	//retorno=system(comando);
	exit(retorno);
	
	
}
