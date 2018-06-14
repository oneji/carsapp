#!/bin/bash

HOST=ftp://cars.55soft.net  #This is the FTP servers host or IP address.
USER=projectEHD55familySWcars             #This is the FTP user that has access to the server.
PASS=YW4C%S%4s          #This is the password for the FTP user.

# Call 1. Uses the ftp command with the -inv switches. 
#-i turns off interactive prompting. 
#-n Restrains FTP from attempting the auto-login feature. 
#-v enables verbose and progress. 

ftp -inv $HOST << EOF

user $USER $PASS

cd /path/to/file

put dist/index.html

# End FTP Connection
bye

EOF