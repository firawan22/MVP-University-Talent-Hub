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
exports.AppController = void 0;
const common_1 = require("@nestjs/common");
const app_service_1 = require("./app.service");
const auth_guard_1 = require("./auth/auth.guard");
const roles_decorator_1 = require("./auth/roles.decorator");
const roles_guard_1 = require("./auth/roles.guard");
const user_decorator_1 = require("./auth/user.decorator");
let AppController = class AppController {
    appService;
    constructor(appService) {
        this.appService = appService;
    }
    getHello() {
        return 'University Talent Hub API is running';
    }
    getHealth() {
        return { status: 'ok', service: 'University Talent Hub API' };
    }
    getDashboard() {
        return this.appService.getDashboardStats();
    }
    getMyProfile(user) {
        return this.appService.getMyProfile(user.id);
    }
    updateMyProfile(body, user) {
        return this.appService.updateMyProfile(user.id, body);
    }
    async getRewards() {
        return this.appService.getRewards();
    }
    createReward(body) {
        return this.appService.createReward(body);
    }
    updateReward(id, body) {
        return this.appService.updateReward(Number(id), body);
    }
    deleteReward(id) {
        return this.appService.deleteReward(Number(id));
    }
    async redeemReward(id, studentId, authorization) {
        const token = authorization?.startsWith('Bearer ') ? authorization.slice(7) : authorization;
        const user = token ? await this.appService.verifyToken(token) : null;
        if (!user) {
            return { error: 'Unauthorized' };
        }
        const sid = user.role === 'admin' ? Number(studentId) : user.id;
        return this.appService.redeemReward(sid, Number(id));
    }
    getLeaderboard() {
        return this.appService.getLeaderboard();
    }
};
exports.AppController = AppController;
__decorate([
    (0, common_1.Get)(),
    __metadata("design:type", Function),
    __metadata("design:paramtypes", []),
    __metadata("design:returntype", String)
], AppController.prototype, "getHello", null);
__decorate([
    (0, common_1.Get)('health'),
    __metadata("design:type", Function),
    __metadata("design:paramtypes", []),
    __metadata("design:returntype", void 0)
], AppController.prototype, "getHealth", null);
__decorate([
    (0, common_1.Get)('dashboard'),
    __metadata("design:type", Function),
    __metadata("design:paramtypes", []),
    __metadata("design:returntype", void 0)
], AppController.prototype, "getDashboard", null);
__decorate([
    (0, common_1.Get)('me/profile'),
    (0, common_1.UseGuards)(auth_guard_1.AuthGuard),
    __param(0, (0, user_decorator_1.User)()),
    __metadata("design:type", Function),
    __metadata("design:paramtypes", [Object]),
    __metadata("design:returntype", void 0)
], AppController.prototype, "getMyProfile", null);
__decorate([
    (0, common_1.Put)('me/profile'),
    (0, common_1.UseGuards)(auth_guard_1.AuthGuard),
    __param(0, (0, common_1.Body)()),
    __param(1, (0, user_decorator_1.User)()),
    __metadata("design:type", Function),
    __metadata("design:paramtypes", [Object, Object]),
    __metadata("design:returntype", void 0)
], AppController.prototype, "updateMyProfile", null);
__decorate([
    (0, common_1.Get)('rewards'),
    __metadata("design:type", Function),
    __metadata("design:paramtypes", []),
    __metadata("design:returntype", Promise)
], AppController.prototype, "getRewards", null);
__decorate([
    (0, common_1.Post)('rewards'),
    (0, common_1.UseGuards)(auth_guard_1.AuthGuard, roles_guard_1.RolesGuard),
    (0, roles_decorator_1.Roles)('admin'),
    __param(0, (0, common_1.Body)()),
    __metadata("design:type", Function),
    __metadata("design:paramtypes", [Object]),
    __metadata("design:returntype", void 0)
], AppController.prototype, "createReward", null);
__decorate([
    (0, common_1.Put)('rewards/:id'),
    (0, common_1.UseGuards)(auth_guard_1.AuthGuard, roles_guard_1.RolesGuard),
    (0, roles_decorator_1.Roles)('admin'),
    __param(0, (0, common_1.Param)('id')),
    __param(1, (0, common_1.Body)()),
    __metadata("design:type", Function),
    __metadata("design:paramtypes", [String, Object]),
    __metadata("design:returntype", void 0)
], AppController.prototype, "updateReward", null);
__decorate([
    (0, common_1.Delete)('rewards/:id'),
    (0, common_1.UseGuards)(auth_guard_1.AuthGuard, roles_guard_1.RolesGuard),
    (0, roles_decorator_1.Roles)('admin'),
    __param(0, (0, common_1.Param)('id')),
    __metadata("design:type", Function),
    __metadata("design:paramtypes", [String]),
    __metadata("design:returntype", void 0)
], AppController.prototype, "deleteReward", null);
__decorate([
    (0, common_1.Post)('rewards/:id/redeem'),
    __param(0, (0, common_1.Param)('id')),
    __param(1, (0, common_1.Query)('studentId')),
    __param(2, (0, common_1.Headers)('authorization')),
    __metadata("design:type", Function),
    __metadata("design:paramtypes", [String, String, String]),
    __metadata("design:returntype", Promise)
], AppController.prototype, "redeemReward", null);
__decorate([
    (0, common_1.Get)('leaderboard'),
    __metadata("design:type", Function),
    __metadata("design:paramtypes", []),
    __metadata("design:returntype", void 0)
], AppController.prototype, "getLeaderboard", null);
exports.AppController = AppController = __decorate([
    (0, common_1.Controller)(),
    __metadata("design:paramtypes", [app_service_1.AppService])
], AppController);
//# sourceMappingURL=app.controller.js.map