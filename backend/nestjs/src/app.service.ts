import { Injectable } from '@nestjs/common';
import * as jwt from 'jsonwebtoken';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { StudentEntity } from './entities/student.entity';
import { SubmissionEntity } from './entities/submission.entity';
import { RewardEntity } from './entities/reward.entity';
import { UserEntity } from './entities/user.entity';

export type Role = 'admin' | 'student';

export interface UserProfile {
  id: number;
  name: string;
  email: string;
  role: Role;
  points: number;
}

export interface DashboardStats {
  totalStudents: number;
  totalSkills: number;
  totalProjects: number;
  pendingReviews: number;
}

export interface StudentProfile {
  id: number;
  name: string;
  major: string;
  bio?: string;
  skills: string[];
  certificates: string[];
  portfolios: string[];
  points: number;
}

export interface SubmissionResult {
  id: number;
  studentId: number;
  title: string;
  description: string;
  evidence?: string;
  status: 'pending' | 'approved' | 'rejected';
  pointsAwarded: number;
}

export interface RewardItem {
  id: number;
  name: string;
  pointsRequired: number;
  description: string;
}

export interface LeaderboardItem {
  rank: number;
  name: string;
  points: number;
}

@Injectable()
export class AppService {
  constructor(
    @InjectRepository(StudentEntity)
    private readonly studentRepo: Repository<StudentEntity>,
    @InjectRepository(SubmissionEntity)
    private readonly submissionRepo: Repository<SubmissionEntity>,
    @InjectRepository(UserEntity)
    private readonly userRepo: Repository<UserEntity>,
    @InjectRepository(RewardEntity)
    private readonly rewardRepo: Repository<RewardEntity>,
  ) {}

  signToken(user: UserProfile): string {
    const payload = { id: user.id, role: user.role, name: user.name };
    const secret = process.env.JWT_SECRET ?? 'dev-secret';
    return jwt.sign(payload, secret, { expiresIn: '7d' });
  }

  async verifyToken(token: string): Promise<UserProfile | null> {
    const secret = process.env.JWT_SECRET ?? 'dev-secret';
    try {
      const payload = jwt.verify(token, secret) as any;
      const user = await this.userRepo.findOne({ where: { id: payload.id } });
      if (!user) return null;
      return { id: user.id, name: user.name, email: user.email, role: user.role as Role, points: user.points };
    } catch (e) {
      return null;
    }
  }

  async getDashboardStats(): Promise<DashboardStats> {
    const totalStudents = await this.studentRepo.count();
    const students = await this.studentRepo.find();
    const totalSkills = students.reduce((sum, s) => sum + (s.skills?.length || 0), 0);
    const totalProjects = await this.submissionRepo.count();
    const pendingReviews = await this.submissionRepo.count({ where: { status: 'pending' } });
    return { totalStudents, totalSkills, totalProjects, pendingReviews };
  }

  async getStudents(): Promise<StudentProfile[]> {
    const students = await this.studentRepo.find();
    return students.map((s) => ({ id: s.id, name: s.name, major: s.major, bio: s.bio || undefined, skills: s.skills || [], certificates: s.certificates || [], portfolios: s.portfolios || [], points: s.points }));
  }

  async getStudentById(id: number): Promise<StudentProfile | null> {
    const s = await this.studentRepo.findOne({ where: { id } });
    if (!s) return null;
    return { id: s.id, name: s.name, major: s.major, bio: s.bio || undefined, skills: s.skills || [], certificates: s.certificates || [], portfolios: s.portfolios || [], points: s.points };
  }

  async getMyProfile(id: number): Promise<StudentProfile | null> {
    return this.getStudentById(id);
  }

  async updateMyProfile(
    id: number,
    payload: Partial<StudentProfile> & { skills?: string | string[]; certificates?: string | string[]; portfolios?: string | string[] },
  ): Promise<StudentProfile | null> {
    const student = await this.studentRepo.findOne({ where: { id } });
    if (!student) return null;

    if (payload.name) student.name = payload.name;
    if (payload.major !== undefined) student.major = payload.major;
    if (payload.bio !== undefined) student.bio = payload.bio;

    const toArray = (value: string | string[] | undefined) => {
      if (!value) return [];
      if (Array.isArray(value)) return value.filter(Boolean);
      return value
        .split(',')
        .map((item) => item.trim())
        .filter(Boolean);
    };

    if (payload.skills !== undefined) student.skills = toArray(payload.skills);
    if (payload.certificates !== undefined) student.certificates = toArray(payload.certificates);
    if (payload.portfolios !== undefined) student.portfolios = toArray(payload.portfolios);

    const saved = await this.studentRepo.save(student);
    return { id: saved.id, name: saved.name, major: saved.major, bio: saved.bio || undefined, skills: saved.skills || [], certificates: saved.certificates || [], portfolios: saved.portfolios || [], points: saved.points };
  }

  async getSubmissions(): Promise<SubmissionResult[]> {
    const subs = await this.submissionRepo.find({ order: { id: 'DESC' } as any });
    return subs.map((s) => ({ id: s.id, studentId: s.studentId, title: s.title, description: s.description, evidence: s.evidence, status: s.status, pointsAwarded: s.pointsAwarded }));
  }

  async createSubmission(studentId: number, payload: Pick<SubmissionResult, 'title' | 'description' | 'evidence'>): Promise<SubmissionResult> {
    const submission = this.submissionRepo.create({ studentId, title: payload.title, description: payload.description, evidence: payload.evidence, status: 'pending', pointsAwarded: 0 });
    const saved = await this.submissionRepo.save(submission);
    return { id: saved.id, studentId: saved.studentId, title: saved.title, description: saved.description, evidence: saved.evidence, status: saved.status, pointsAwarded: saved.pointsAwarded };
  }

  async getRewards(): Promise<RewardItem[]> {
    const rewards = await this.rewardRepo.find({ order: { id: 'ASC' } as any });
    return rewards.map((reward) => ({ id: reward.id, name: reward.name, pointsRequired: reward.pointsRequired, description: reward.description }));
  }

  async createReward(body: { name: string; description: string; pointsRequired: number }): Promise<RewardItem> {
    const reward = this.rewardRepo.create({ name: body.name, description: body.description, pointsRequired: body.pointsRequired });
    const saved = await this.rewardRepo.save(reward);
    return { id: saved.id, name: saved.name, pointsRequired: saved.pointsRequired, description: saved.description };
  }

  async updateReward(id: number, body: Partial<{ name: string; description: string; pointsRequired: number }>): Promise<RewardItem | null> {
    const reward = await this.rewardRepo.findOne({ where: { id } });
    if (!reward) return null;
    if (body.name !== undefined) reward.name = body.name;
    if (body.description !== undefined) reward.description = body.description;
    if (body.pointsRequired !== undefined) reward.pointsRequired = body.pointsRequired;
    const saved = await this.rewardRepo.save(reward);
    return { id: saved.id, name: saved.name, pointsRequired: saved.pointsRequired, description: saved.description };
  }

  async deleteReward(id: number): Promise<{ success: boolean }> {
    const result = await this.rewardRepo.delete(id);
    return { success: (result.affected ?? 0) > 0 };
  }

  async redeemReward(studentId: number, rewardId: number) {
    const reward = await this.rewardRepo.findOne({ where: { id: rewardId } });
    const student = await this.userRepo.findOne({ where: { id: studentId } });
    const studentProfile = await this.studentRepo.findOne({ where: { id: studentId } });

    if (!reward || !student) {
      return { success: false, message: 'Student or reward not found.' };
    }

    if ((student.points || 0) < reward.pointsRequired) {
      return { success: false, message: 'Insufficient points to redeem this reward.' };
    }

    student.points = (student.points || 0) - reward.pointsRequired;
    await this.userRepo.save(student);

    // Keep StudentEntity points in sync
    if (studentProfile) {
      studentProfile.points = student.points;
      await this.studentRepo.save(studentProfile);
    }

    return {
      success: true,
      message: `Redeemed ${reward.name}. Remaining points: ${student.points}`,
      reward,
      remainingPoints: student.points,
    };
  }

  async getLeaderboard(): Promise<LeaderboardItem[]> {
    const students = await this.userRepo.find({ where: { role: 'student' } });
    const sorted = students.sort((a, b) => (b.points || 0) - (a.points || 0));
    return sorted.map((u, i) => ({ rank: i + 1, name: u.name, points: u.points }));
  }
}
