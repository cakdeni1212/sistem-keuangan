#!/bin/bash
# Start MAMP MySQL 8.0 untuk Sistem Keuangan
MYSQL_BIN=/Applications/MAMP/Library/bin/mysql80/bin
MYSQL_DATA=/Applications/MAMP/db/mysql80
MYSQL_BASE=/Applications/MAMP/Library/bin/mysql80

if pgrep mysqld > /dev/null 2>&1; then
    echo "✅ MySQL sudah berjalan"
else
    "$MYSQL_BIN/mysqld" --basedir="$MYSQL_BASE" --datadir="$MYSQL_DATA" \
      --socket=/tmp/mysql.sock --port=3306 --pid-file=/tmp/mysql.pid \
      --user=deniubaidillah --daemonize
    sleep 2
    pgrep mysqld > /dev/null && echo "✅ MySQL berhasil distart (port 3306)" || echo "❌ MySQL gagal start"
fi
