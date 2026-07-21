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
export declare class AppService {
    private readonly studentRepo;
    private readonly submissionRepo;
    private readonly userRepo;
    private readonly rewardRepo;
    constructor(studentRepo: Repository<StudentEntity>, submissionRepo: Repository<SubmissionEntity>, userRepo: Repository<UserEntity>, rewardRepo: Repository<RewardEntity>);
    signToken(user: UserProfile): string;
    verifyToken(token: string): Promise<UserProfile | null>;
    getDashboardStats(): Promise<DashboardStats>;
    getStudents(): Promise<StudentProfile[]>;
    getStudentById(id: number): Promise<StudentProfile | null>;
    getMyProfile(id: number): Promise<StudentProfile | null>;
    updateMyProfile(id: number, payload: Partial<StudentProfile> & {
        skills?: string | string[];
        certificates?: string | string[];
        portfolios?: string | string[];
    }): Promise<StudentProfile | null>;
    getSubmissions(): Promise<SubmissionResult[]>;
    createSubmission(studentId: number, payload: Pick<SubmissionResult, 'title' | 'description' | 'evidence'>): Promise<SubmissionResult>;
    getRewards(): Promise<RewardItem[]>;
    createReward(body: {
        name: string;
        description: string;
        pointsRequired: number;
    }): Promise<RewardItem>;
    updateReward(id: number, body: Partial<{
        name: string;
        description: string;
        pointsRequired: number;
    }>): Promise<RewardItem | null>;
    deleteReward(id: number): Promise<{
        success: boolean;
    }>;
    redeemReward(studentId: number, rewardId: number): Promise<{
        success: boolean;
        message: string;
        reward?: undefined;
        remainingPoints?: undefined;
    } | {
        success: boolean;
        message: string;
        reward: RewardEntity;
        remainingPoints: number;
    }>;
    getLeaderboard(): Promise<LeaderboardItem[]>;
}
