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
exports.AuthService = void 0;
const common_1 = require("@nestjs/common");
const typeorm_1 = require("@nestjs/typeorm");
const typeorm_2 = require("typeorm");
const user_entity_1 = require("../entities/user.entity");
const student_entity_1 = require("../entities/student.entity");
const app_service_1 = require("../app.service");
const bcrypt_1 = require("./bcrypt");
let AuthService = class AuthService {
    usersRepo;
    studentRepo;
    appService;
    constructor(usersRepo, studentRepo, appService) {
        this.usersRepo = usersRepo;
        this.studentRepo = studentRepo;
        this.appService = appService;
    }
    async validateUser(email, password) {
        const user = await this.usersRepo.findOne({ where: { email } });
        if (!user || !user.passwordHash)
            return null;
        if (!(0, bcrypt_1.comparePassword)(password, user.passwordHash))
            return null;
        return user;
    }
    async login(email, password) {
        const user = await this.validateUser(email, password);
        if (!user)
            return null;
        const token = this.appService.signToken({
            id: user.id,
            name: user.name,
            role: user.role,
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
    async register(email, password, name, role) {
        const existing = await this.usersRepo.findOne({ where: { email } });
        if (existing)
            throw new common_1.ConflictException('Email already registered');
        const user = this.usersRepo.create({
            email,
            name,
            passwordHash: (0, bcrypt_1.hashPassword)(password),
            role: role || 'student',
            points: 0,
        });
        const saved = await this.usersRepo.save(user);
        if (saved.role === 'student') {
            const profile = this.studentRepo.create({ name, points: 0 });
            await this.studentRepo.save(profile);
        }
        const token = this.appService.signToken({
            id: saved.id,
            name: saved.name,
            role: saved.role,
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
};
exports.AuthService = AuthService;
exports.AuthService = AuthService = __decorate([
    (0, common_1.Injectable)(),
    __param(0, (0, typeorm_1.InjectRepository)(user_entity_1.UserEntity)),
    __param(1, (0, typeorm_1.InjectRepository)(student_entity_1.StudentEntity)),
    __metadata("design:paramtypes", [typeorm_2.Repository,
        typeorm_2.Repository,
        app_service_1.AppService])
], AuthService);
//# sourceMappingURL=auth.service.js.map