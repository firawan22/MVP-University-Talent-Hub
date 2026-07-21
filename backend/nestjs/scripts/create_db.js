const mysql = require('mysql2/promise');
const path = require('path');
require('dotenv').config({ path: path.join(__dirname, '..', '.env') });

async function createDatabase() {
  const host = process.env.DB_HOST || '127.0.0.1';
  const port = Number(process.env.DB_PORT || 3306);
  const user = process.env.DB_USER || 'root';
  const password = process.env.DB_PASS || '';
  const dbName = process.env.DB_NAME || 'talent_hub';

  try {
    const connection = await mysql.createConnection({ host, port, user, password });
    console.log(`Connected to MySQL on ${host}:${port} as ${user}`);
    await connection.query(`CREATE DATABASE IF NOT EXISTS \`${dbName}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;`);
    console.log(`Database '${dbName}' ensured.`);
    await connection.end();
    process.exit(0);
  } catch (err) {
    console.error('Failed to create database:', err.message || err);
    process.exit(1);
  }
}

createDatabase();
