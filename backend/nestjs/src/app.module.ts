import { Global, Module } from '@nestjs/common';
import { ConfigModule } from '@nestjs/config';
import { TypeOrmModule } from '@nestjs/typeorm';
import { AppController } from './app.controller';
import { AppService } from './app.service';
import { SubmissionsController } from './submissions/submissions.controller';
import { SubmissionsService } from './submissions/submissions.service';
import { UserEntity } from './entities/user.entity';
import { StudentEntity } from './entities/student.entity';
import { SubmissionEntity } from './entities/submission.entity';
import { RewardEntity } from './entities/reward.entity';
import { OpportunityEntity } from './entities/opportunity.entity';
import { NotificationEntity } from './entities/notification.entity';
import { PointConfigurationEntity } from './entities/point-configuration.entity';
import { AuthController } from './auth/auth.controller';
import { AuthService } from './auth/auth.service';
import { StudentsController } from './students/students.controller';
import { StudentsService } from './students/students.service';
import { UploadModule } from './upload/upload.module';
import { OpportunitiesModule } from './opportunities/opportunities.module';
import { NotificationsModule } from './notifications/notifications.module';
import { RecommendationsModule } from './recommendations/recommendations.module';
import { PointConfigurationsModule } from './point-configurations/point-configurations.module';

@Global()
@Module({
  imports: [
    ConfigModule.forRoot({ isGlobal: true }),
    TypeOrmModule.forRootAsync({
      useFactory: () => ({
        type: 'mysql',
        host: process.env.DB_HOST || '127.0.0.1',
        port: Number(process.env.DB_PORT || 3306),
        username: process.env.DB_USER || 'root',
        password: process.env.DB_PASS || '',
        database: process.env.DB_NAME || 'talent_hub',
        entities: [UserEntity, StudentEntity, SubmissionEntity, RewardEntity, OpportunityEntity, NotificationEntity, PointConfigurationEntity],
        synchronize: true,
      }),
    }),
    TypeOrmModule.forFeature([UserEntity, StudentEntity, SubmissionEntity, RewardEntity, OpportunityEntity, NotificationEntity, PointConfigurationEntity]),
    UploadModule,
    OpportunitiesModule,
    NotificationsModule,
    RecommendationsModule,
    PointConfigurationsModule,
  ],
  controllers: [AppController, SubmissionsController, AuthController, StudentsController],
  providers: [AppService, SubmissionsService, AuthService, StudentsService],
  exports: [AppService],
})
export class AppModule {}
