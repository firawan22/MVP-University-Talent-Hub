import * as bcrypt from 'bcryptjs';

export function hashPassword(password: string): string {
  // 10 rounds is acceptable for development; increase in production
  return bcrypt.hashSync(password, 10);
}

export function comparePassword(password: string, hash: string): boolean {
  return bcrypt.compareSync(password, hash);
}
