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

    const rewards = [
      { name: 'Voucher Kantin', pointsRequired: 100, description: 'Voucher makan senilai Rp 20.000' },
      { name: 'E-Book Bundle', pointsRequired: 200, description: 'Akses materi belajar premium' },
      { name: 'Priority Internship Slot', pointsRequired: 300, description: 'Prioritas seleksi program magang' },
    ];

    for (const reward of rewards) {
      const [rows] = await conn.query('SELECT id FROM rewards WHERE name = ?', [reward.name]);
      if (!rows.length) {
        await conn.query('INSERT INTO rewards (name, pointsRequired, description) VALUES (?, ?, ?)', [reward.name, reward.pointsRequired, reward.description]);
        console.log(`Inserted reward: ${reward.name}`);
      } else {
        console.log(`Reward already exists: ${reward.name}`);
      }
    }

    console.log('Reward seeding completed.');
    await conn.end();
    process.exit(0);
  } catch (err) {
    console.error('Seeding failed:', err.message || err);
    if (conn) await conn.end();
    process.exit(1);
  }
}

seed();
