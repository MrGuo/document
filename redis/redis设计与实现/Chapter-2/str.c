/*************************************************************************
	> File Name: str.c
	> Author: 
	> Mail: 
	> Created Time: 四  3/ 9 15:41:51 2017
 ************************************************************************/

#include<stdio.h>
#include<string.h>

int main()
{
    char *pmessage = "Hello world\n";
    char *lmessage = "Hello world\n";
    char amessage[] = "Hello world\n";
    char amessage2[] = "Hello world\n";


    printf("%p\n%p\n%p\n%p\n", pmessage, lmessage, amessage, amessage2);



    amessage[1] = 'Y';

    printf("%ld, %ld\n", strlen(pmessage), strlen(amessage));

    return 0;
}
