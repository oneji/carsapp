read -s -p "Enter Password: " mypassword
ncftp <<EOF
open -u projectEHD55familySWcars -p $mypassword ftp://cars.55soft.net
cd /
lcd /dist
put -R *
bye
EOF