import { Injectable, ConflictException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { UserEntity } from '../entities/user.entity';
import { StudentEntity } from '../entities/student.entity';
import { AppService } from '../app.service';
import { comparePassword, hashPassword } from './bcrypt';

@Injectable()
export class AuthService {
  constructor(
    @InjectRepository(UserEntity) private usersRepo: Repository<UserEntity>,
    @InjectRepository(StudentEntity) private studentRepo: Repository<StudentEntity>,
    private readonly appService: AppService,
  ) {}

  async validateUser(email: string, password: string): Promise<UserEntity | null> {
    const user = await this.usersRepo.findOne({ where: { email } });
    if (!user || !user.passwordHash) return null;
    if (!comparePassword(password, user.passwordHash)) return null;
    return user;
  }

  async login(email: string, password: string) {
    const user = await this.validateUser(email, password);
    if (!user) return null;

    const token = this.appService.signToken({
      id: user.id,
      name: user.name,
      role: user.role as any,
      email: user.email,
      points: user.points,
    });

    return {
      token,
      user: {
        id: user.id,
        name: user.name,
        email: user.email,
        role: user.role,
        points: user.points,
      },
    };
  }

  async register(email: string, password: string, name: string, role?: string) {
    const existing = await this.usersRepo.findOne({ where: { email } });
    if (existing) throw new ConflictException('Email already registered');

    const user = this.usersRepo.create({
      email,
      name,
      passwordHash: hashPassword(password),
      role: role || 'student',
      points: 0,
    });
    const saved = await this.usersRepo.save(user);

    // Auto-create student profile for student role
    if (saved.role === 'student') {
      const profile = this.studentRepo.create({ name, points: 0 });
      await this.studentRepo.save(profile);
    }

    const token = this.appService.signToken({
      id: saved.id,
      name: saved.name,
      role: saved.role as any,
      email: saved.email,
      points: saved.points,
    });

    return {
      token,
      user: {
        id: saved.id,
        name: saved.name,
        email: saved.email,
        role: saved.role,
        points: saved.points,
      },
    };
  }
}
