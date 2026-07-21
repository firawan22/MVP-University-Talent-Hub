"use strict";
var __createBinding = (this && this.__createBinding) || (Object.create ? (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    var desc = Object.getOwnPropertyDescriptor(m, k);
    if (!desc || ("get" in desc ? !m.__esModule : desc.writable || desc.configurable)) {
      desc = { enumerable: true, get: function() { return m[k]; } };
    }
    Object.defineProperty(o, k2, desc);
}) : (function(o, m, k, k2) {
    if (k2 === undefined) k2 = k;
    o[k2] = m[k];
}));
var __setModuleDefault = (this && this.__setModuleDefault) || (Object.create ? (function(o, v) {
    Object.defineProperty(o, "default", { enumerable: true, value: v });
}) : function(o, v) {
    o["default"] = v;
});
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __importStar = (this && this.__importStar) || (function () {
    var ownKeys = function(o) {
        ownKeys = Object.getOwnPropertyNames || function (o) {
            var ar = [];
            for (var k in o) if (Object.prototype.hasOwnProperty.call(o, k)) ar[ar.length] = k;
            return ar;
        };
        return ownKeys(o);
    };
    return function (mod) {
        if (mod && mod.__esModule) return mod;
        var result = {};
        if (mod != null) for (var k = ownKeys(mod), i = 0; i < k.length; i++) if (k[i] !== "default") __createBinding(result, mod, k[i]);
        __setModuleDefault(result, mod);
        return result;
    };
})();
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};
var __param = (this && this.__param) || function (paramIndex, decorator) {
    return function (target, key) { decorator(target, key, paramIndex); }
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.AppService = void 0;
const common_1 = require("@nestjs/common");
const jwt = __importStar(require("jsonwebtoken"));
const typeorm_1 = require("@nestjs/typeorm");
const typeorm_2 = require("typeorm");
const student_entity_1 = require("./entities/student.entity");
const submission_entity_1 = require("./entities/submission.entity");
const reward_entity_1 = require("./entities/reward.entity");
const user_entity_1 = require("./entities/user.entity");
let AppService = class AppService {
    studentRepo;
    submissionRepo;
    userRepo;
    rewardRepo;
    constructor(studentRepo, submissionRepo, userRepo, rewardRepo) {
        this.studentRepo = studentRepo;
        this.submissionRepo = submissionRepo;
        this.userRepo = userRepo;
        this.rewardRepo = rewardRepo;
    }
    signToken(user) {
        const payload = { id: user.id, role: user.role, name: user.name };
        const secret = process.env.JWT_SECRET ?? 'dev-secret';
        return jwt.sign(payload, secret, { expiresIn: '7d' });
    }
    async verifyToken(token) {
        const secret = process.env.JWT_SECRET ?? 'dev-secret';
        try {
            const payload = jwt.verify(token, secret);
            const user = await this.userRepo.findOne({ where: { id: payload.id } });
            if (!user)
                return null;
            return { id: user.id, name: user.name, email: user.email, role: user.role, points: user.points };
        }
        catch (e) {
            return null;
        }
    }
    async getDashboardStats() {
        const totalStudents = await this.studentRepo.count();
        const students = await this.studentRepo.find();
        const totalSkills = students.reduce((sum, s) => sum + (s.skills?.length || 0), 0);
        const totalProjects = await this.submissionRepo.count();
        const pendingReviews = await this.submissionRepo.count({ where: { status: 'pending' } });
        return { totalStudents, totalSkills, totalProjects, pendingReviews };
    }
    async getStudents() {
        const students = await this.studentRepo.find();
        return students.map((s) => ({ id: s.id, name: s.name, major: s.major, bio: s.bio || undefined, skills: s.skills || [], certificates: s.certificates || [], portfolios: s.portfolios || [], points: s.points }));
    }
    async getStudentById(id) {
        const s = await this.studentRepo.findOne({ where: { id } });
        if (!s)
            return null;
        return { id: s.id, name: s.name, major: s.major, bio: s.bio || undefined, skills: s.skills || [], certificates: s.certificates || [], portfolios: s.portfolios || [], points: s.points };
    }
    async getMyProfile(id) {
        return this.getStudentById(id);
    }
    async updateMyProfile(id, payload) {
        const student = await this.studentRepo.findOne({ where: { id } });
        if (!student)
            return null;
        if (payload.name)
            student.name = payload.name;
        if (payload.major !== undefined)
            student.major = payload.major;
        if (payload.bio !== undefined)
            student.bio = payload.bio;
        const toArray = (value) => {
            if (!value)
                return [];
            if (Array.isArray(value))
                return value.filter(Boolean);
            return value
                .split(',')
                .map((item) => item.trim())
                .filter(Boolean);
        };
        if (payload.skills !== undefined)
            student.skills = toArray(payload.skills);
        if (payload.certificates !== undefined)
            student.certificates = toArray(payload.certificates);
        if (payload.portfolios !== undefined)
            student.portfolios = toArray(payload.portfolios);
        const saved = await this.studentRepo.save(student);
        return { id: saved.id, name: saved.name, major: saved.major, bio: saved.bio || undefined, skills: saved.skills || [], certificates: saved.certificates || [], portfolios: saved.portfolios || [], points: saved.points };
    }
    async getSubmissions() {
        const subs = await this.submissionRepo.find({ order: { id: 'DESC' } });
        return subs.map((s) => ({ id: s.id, studentId: s.studentId, title: s.title, description: s.description, evidence: s.evidence, status: s.status, pointsAwarded: s.pointsAwarded }));
    }
    async createSubmission(studentId, payload) {
        const submission = this.submissionRepo.create({ studentId, title: payload.title, description: payload.description, evidence: payload.evidence, status: 'pending', pointsAwarded: 0 });
        const saved = await this.submissionRepo.save(submission);
        return { id: saved.id, studentId: saved.studentId, title: saved.title, description: saved.description, evidence: saved.evidence, status: saved.status, pointsAwarded: saved.pointsAwarded };
    }
    async getRewards() {
        const rewards = await this.rewardRepo.find({ order: { id: 'ASC' } });
        return rewards.map((reward) => ({ id: reward.id, name: reward.name, pointsRequired: reward.pointsRequired, description: reward.description }));
    }
    async createReward(body) {
        const reward = this.rewardRepo.create({ name: body.name, description: body.description, pointsRequired: body.pointsRequired });
        const saved = await this.rewardRepo.save(reward);
        return { id: saved.id, name: saved.name, pointsRequired: saved.pointsRequired, description: saved.description };
    }
    async updateReward(id, body) {
        const reward = await this.rewardRepo.findOne({ where: { id } });
        if (!reward)
            return null;
        if (body.name !== undefined)
            reward.name = body.name;
        if (body.description !== undefined)
            reward.description = body.description;
        if (body.pointsRequired !== undefined)
            reward.pointsRequired = body.pointsRequired;
        const saved = await this.rewardRepo.save(reward);
        return { id: saved.id, name: saved.name, pointsRequired: saved.pointsRequired, description: saved.description };
    }
    async deleteReward(id) {
        const result = await this.rewardRepo.delete(id);
        return { success: (result.affected ?? 0) > 0 };
    }
    async redeemReward(studentId, rewardId) {
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
    async getLeaderboard() {
        const students = await this.userRepo.find({ where: { role: 'student' } });
        const sorted = students.sort((a, b) => (b.points || 0) - (a.points || 0));
        return sorted.map((u, i) => ({ rank: i + 1, name: u.name, points: u.points }));
    }
};
exports.AppService = AppService;
exports.AppService = AppService = __decorate([
    (0, common_1.Injectable)(),
    __param(0, (0, typeorm_1.InjectRepository)(student_entity_1.StudentEntity)),
    __param(1, (0, typeorm_1.InjectRepository)(submission_entity_1.SubmissionEntity)),
    __param(2, (0, typeorm_1.InjectRepository)(user_entity_1.UserEntity)),
    __param(3, (0, typeorm_1.InjectRepository)(reward_entity_1.RewardEntity)),
    __metadata("design:paramtypes", [typeorm_2.Repository,
        typeorm_2.Repository,
        typeorm_2.Repository,
        typeorm_2.Repository])
], AppService);
//# sourceMappingURL=app.service.js.map