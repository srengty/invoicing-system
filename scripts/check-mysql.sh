#!/bin/bash

# Settings (change as needed)
DB_HOST="localhost"
DB_USER="invoices"
DB_PASS="invoice@gicitc"
DB_NAME="invoices"
TABLE_NAME="customers"

echo "✅ Checking MySQL server..."

# Check MySQL connection
if ! mysqladmin ping -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" --silent; then
  echo "❌ MySQL is not reachable"
  exit 1
fi

echo "✅ MySQL server is reachable"

# Check if database exists
if ! mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" -e "USE ${DB_NAME};" 2>/dev/null; then
  echo "❌ Database '$DB_NAME' does not exist"
  exit 1
fi

echo "✅ Database '$DB_NAME' exists"

# Show specific table contents (optional: LIMIT)
echo "📦 Showing data from table '$TABLE_NAME':"
mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" -D "$DB_NAME" -e "SELECT * FROM ${TABLE_NAME} LIMIT 5;"
