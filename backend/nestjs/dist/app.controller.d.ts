import { AppService } from './app.service';
import type { StudentProfile, UserProfile } from './app.service';
export declare class AppController {
    private readonly appService;
    constructor(appService: AppService);
    getHello(): string;
    getHealth(): {
        status: string;
        service: string;
    };
    getDashboard(): Promise<import("./app.service").DashboardStats>;
    getMyProfile(user: UserProfile): Promise<StudentProfile | null>;
    updateMyProfile(body: Partial<StudentProfile> & {
        skills?: string | string[];
        certificates?: string | string[];
        portfolios?: string | string[];
    }, user: UserProfile): Promise<StudentProfile | null>;
    getRewards(): Promise<import("./app.service").RewardItem[]>;
    createReward(body: {
        name: string;
        description: string;
        pointsRequired: number;
    }): Promise<import("./app.service").RewardItem>;
    updateReward(id: string, body: {
        name?: string;
        description?: string;
        pointsRequired?: number;
    }): Promise<import("./app.service").RewardItem | null>;
    deleteReward(id: string): Promise<{
        success: boolean;
    }>;
    redeemReward(id: string, studentId: string, authorization?: string): Promise<{
        success: boolean;
        message: string;
        reward?: undefined;
        remainingPoints?: undefined;
    } | {
        success: boolean;
        message: string;
        reward: import("./entities/reward.entity").RewardEntity;
        remainingPoints: number;
    } | {
        error: string;
    }>;
    getLeaderboard(): Promise<import("./app.service").LeaderboardItem[]>;
}
