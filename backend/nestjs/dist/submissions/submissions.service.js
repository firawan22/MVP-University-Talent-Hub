"use strict";
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
var __metadata = (this && this.__metadata) || function (k, v) {
    if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(k, v);
};
var __param = (this && this.__param) || function (paramIndex, decorator) {
    return function (target, key) { decorator(target, key, paramIndex); }
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.SubmissionsService = void 0;
const common_1 = require("@nestjs/common");
const typeorm_1 = require("@nestjs/typeorm");
const typeorm_2 = require("typeorm");
const submission_entity_1 = require("../entities/submission.entity");
const user_entity_1 = require("../entities/user.entity");
const student_entity_1 = require("../entities/student.entity");
const point_configuration_entity_1 = require("../entities/point-configuration.entity");
const notification_entity_1 = require("../entities/notification.entity");
let SubmissionsService = class SubmissionsService {
    submissionsRepo;
    usersRepo;
    studentRepo;
    pointConfigRepo;
    notificationRepo;
    constructor(submissionsRepo, usersRepo, studentRepo, pointConfigRepo, notificationRepo) {
        this.submissionsRepo = submissionsRepo;
        this.usersRepo = usersRepo;
        this.studentRepo = studentRepo;
        this.pointConfigRepo = pointConfigRepo;
        this.notificationRepo = notificationRepo;
    }
    async getAll() {
        return this.submissionsRepo.find({ order: { id: 'DESC' } });
    }
    async createSubmission(studentId, payload) {
        const submission = this.submissionsRepo.create({
            studentId,
            title: payload.title,
            description: payload.description,
            evidence: payload.evidence,
            submissionType: payload.submissionType || 'project',
            status: 'pending',
            pointsAwarded: 0,
        });
        const saved = await this.submissionsRepo.save(submission);
        await this.notificationRepo.save(this.notificationRepo.create({
            userId: studentId,
            title: 'Submission Created',
            message: `Your submission "${payload.title}" has been sent for review.`,
        }));
        const admins = await this.usersRepo.find({ where: { role: 'admin' } });
        for (const admin of admins) {
            await this.notificationRepo.save(this.notificationRepo.create({
                userId: admin.id,
                title: 'New Submission',
                message: `A new submission "${payload.title}" is pending your review.`,
                link: '/admin/reviews',
            }));
        }
        return saved;
    }
    async reviewSubmission(id, decision) {
        const submission = await this.submissionsRepo.findOne({ where: { id } });
        if (!submission)
            return null;
        if (decision === 'approved') {
            submission.status = 'approved';
            const pointConfig = await this.pointConfigRepo.findOne({ where: { type: submission.submissionType } });
            submission.pointsAwarded = pointConfig ? pointConfig.points : 50;
            await this.submissionsRepo.save(submission);
            const studentUser = await this.usersRepo.findOne({ where: { id: submission.studentId } });
            if (studentUser) {
                studentUser.points = (studentUser.points || 0) + submission.pointsAwarded;
                await this.usersRepo.save(studentUser);
            }
            const studentProfile = await this.studentRepo.findOne({ where: { id: submission.studentId } });
            if (studentProfile) {
                studentProfile.points = (studentProfile.points || 0) + submission.pointsAwarded;
                await this.studentRepo.save(studentProfile);
            }
        }
        else {
            submission.status = 'rejected';
            submission.pointsAwarded = 0;
            await this.submissionsRepo.save(submission);
        }
        return submission;
    }
};
exports.SubmissionsService = SubmissionsService;
exports.SubmissionsService = SubmissionsService = __decorate([
    (0, common_1.Injectable)(),
    __param(0, (0, typeorm_1.InjectRepository)(submission_entity_1.SubmissionEntity)),
    __param(1, (0, typeorm_1.InjectRepository)(user_entity_1.UserEntity)),
    __param(2, (0, typeorm_1.InjectRepository)(student_entity_1.StudentEntity)),
    __param(3, (0, typeorm_1.InjectRepository)(point_configuration_entity_1.PointConfigurationEntity)),
    __param(4, (0, typeorm_1.InjectRepository)(notification_entity_1.NotificationEntity)),
    __metadata("design:paramtypes", [typeorm_2.Repository,
        typeorm_2.Repository,
        typeorm_2.Repository,
        typeorm_2.Repository,
        typeorm_2.Repository])
], SubmissionsService);
//# sourceMappingURL=submissions.service.js.map