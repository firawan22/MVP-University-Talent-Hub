"use strict";
var __decorate = (this && this.__decorate) || function (decorators, target, key, desc) {
    var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
    if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
    else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
    return c > 3 && r && Object.defineProperty(target, key, r), r;
};
Object.defineProperty(exports, "__esModule", { value: true });
exports.AppModule = void 0;
const common_1 = require("@nestjs/common");
const config_1 = require("@nestjs/config");
const typeorm_1 = require("@nestjs/typeorm");
const app_controller_1 = require("./app.controller");
const app_service_1 = require("./app.service");
const submissions_controller_1 = require("./submissions/submissions.controller");
const submissions_service_1 = require("./submissions/submissions.service");
const user_entity_1 = require("./entities/user.entity");
const student_entity_1 = require("./entities/student.entity");
const submission_entity_1 = require("./entities/submission.entity");
const reward_entity_1 = require("./entities/reward.entity");
const opportunity_entity_1 = require("./entities/opportunity.entity");
const notification_entity_1 = require("./entities/notification.entity");
const point_configuration_entity_1 = require("./entities/point-configuration.entity");
const auth_controller_1 = require("./auth/auth.controller");
const auth_service_1 = require("./auth/auth.service");
const students_controller_1 = require("./students/students.controller");
const students_service_1 = require("./students/students.service");
const upload_module_1 = require("./upload/upload.module");
const opportunities_module_1 = require("./opportunities/opportunities.module");
const notifications_module_1 = require("./notifications/notifications.module");
const recommendations_module_1 = require("./recommendations/recommendations.module");
const point_configurations_module_1 = require("./point-configurations/point-configurations.module");
let AppModule = class AppModule {
};
exports.AppModule = AppModule;
exports.AppModule = AppModule = __decorate([
    (0, common_1.Global)(),
    (0, common_1.Module)({
        imports: [
            config_1.ConfigModule.forRoot({ isGlobal: true }),
            typeorm_1.TypeOrmModule.forRootAsync({
                useFactory: () => ({
                    type: 'mysql',
                    host: process.env.DB_HOST || '127.0.0.1',
                    port: Number(process.env.DB_PORT || 3306),
                    username: process.env.DB_USER || 'root',
                    password: process.env.DB_PASS || '',
                    database: process.env.DB_NAME || 'talent_hub',
                    entities: [user_entity_1.UserEntity, student_entity_1.StudentEntity, submission_entity_1.SubmissionEntity, reward_entity_1.RewardEntity, opportunity_entity_1.OpportunityEntity, notification_entity_1.NotificationEntity, point_configuration_entity_1.PointConfigurationEntity],
                    synchronize: true,
                }),
            }),
            typeorm_1.TypeOrmModule.forFeature([user_entity_1.UserEntity, student_entity_1.StudentEntity, submission_entity_1.SubmissionEntity, reward_entity_1.RewardEntity, opportunity_entity_1.OpportunityEntity, notification_entity_1.NotificationEntity, point_configuration_entity_1.PointConfigurationEntity]),
            upload_module_1.UploadModule,
            opportunities_module_1.OpportunitiesModule,
            notifications_module_1.NotificationsModule,
            recommendations_module_1.RecommendationsModule,
            point_configurations_module_1.PointConfigurationsModule,
        ],
        controllers: [app_controller_1.AppController, submissions_controller_1.SubmissionsController, auth_controller_1.AuthController, students_controller_1.StudentsController],
        providers: [app_service_1.AppService, submissions_service_1.SubmissionsService, auth_service_1.AuthService, students_service_1.StudentsService],
        exports: [app_service_1.AppService],
    })
], AppModule);
//# sourceMappingURL=app.module.js.map