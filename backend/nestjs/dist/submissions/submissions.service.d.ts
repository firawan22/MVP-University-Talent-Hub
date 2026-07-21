import { Repository } from 'typeorm';
import { SubmissionEntity } from '../entities/submission.entity';
import { UserEntity } from '../entities/user.entity';
import { StudentEntity } from '../entities/student.entity';
import { PointConfigurationEntity } from '../entities/point-configuration.entity';
import { NotificationEntity } from '../entities/notification.entity';
export declare class SubmissionsService {
    private submissionsRepo;
    private usersRepo;
    private studentRepo;
    private pointConfigRepo;
    private notificationRepo;
    constructor(submissionsRepo: Repository<SubmissionEntity>, usersRepo: Repository<UserEntity>, studentRepo: Repository<StudentEntity>, pointConfigRepo: Repository<PointConfigurationEntity>, notificationRepo: Repository<NotificationEntity>);
    getAll(): Promise<SubmissionEntity[]>;
    createSubmission(studentId: number, payload: {
        title: string;
        description: string;
        evidence?: string;
        submissionType?: string;
    }): Promise<SubmissionEntity>;
    reviewSubmission(id: number, decision: 'approved' | 'rejected'): Promise<SubmissionEntity | null>;
}
