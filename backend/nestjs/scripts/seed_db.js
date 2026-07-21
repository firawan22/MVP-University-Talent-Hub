const mysql = require('mysql2/promise');
const path = require('path');
require('dotenv').config({ path: path.join(__dirname, '..', '.env') });

async function seed() {
  const host = process.env.DB_HOST || '127.0.0.1';
  const port = Number(process.env.DB_PORT || 3306);
  const user = process.env.DB_USER || 'root';
  const password = process.env.DB_PASS || '';
  const dbName = process.env.DB_NAME || 'talent_hub';

  let conn;
  try {
    conn = await mysql.createConnection({ host, port, user, password, database: dbName });
    console.log(`Connected to ${host}:${port} as ${user}, using DB ${dbName}`);

    // Insert admin if not exists
    const [admins] = await conn.query('SELECT id FROM users WHERE email = ?', ['admin@example.com']);
    if (!admins.length) {
      const bcrypt = require('bcryptjs');
      const passwordHash = bcrypt.hashSync('password', 10);
      await conn.query('INSERT INTO users (email, name, role, points, passwordHash) VALUES (?, ?, ?, ?, ?)', ['admin@example.com', 'Administrator', 'admin', 0, passwordHash]);
      console.log('Inserted admin user: admin@example.com / password');
    } else {
      console.log('Admin already exists');
    }

    // Insert sample student if not exists
    const [students] = await conn.query('SELECT id FROM students WHERE name = ?', ['Ayu Pratiwi']);
    if (!students.length) {
      await conn.query('INSERT INTO students (name, major, skills, certificates, portfolios, points) VALUES (?, ?, ?, ?, ?, ?)', [
        'Ayu Pratiwi',
        'Informatika',
        'Laravel,UI/UX Design,Public Speaking',
        'UI/UX Bootcamp 2025,Leadership Certificate',
        'TalentHub Website,Event Poster Design',
        320,
      ]);
      console.log('Inserted sample student: Ayu Pratiwi');
    } else {
      console.log('Sample student already exists');
    }

    console.log('Seeding completed.');
    await conn.end();
    process.exit(0);
  } catch (err) {
    console.error('Seeding failed:', err.message || err);
    if (conn) await conn.end();
    process.exit(1);
  }
}

seed();
