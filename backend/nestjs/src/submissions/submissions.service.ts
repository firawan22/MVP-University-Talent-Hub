import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { SubmissionEntity } from '../entities/submission.entity';
import { UserEntity } from '../entities/user.entity';
import { StudentEntity } from '../entities/student.entity';
import { PointConfigurationEntity } from '../entities/point-configuration.entity';
import { NotificationEntity } from '../entities/notification.entity';

@Injectable()
export class SubmissionsService {
  constructor(
    @InjectRepository(SubmissionEntity) private submissionsRepo: Repository<SubmissionEntity>,
    @InjectRepository(UserEntity) private usersRepo: Repository<UserEntity>,
    @InjectRepository(StudentEntity) private studentRepo: Repository<StudentEntity>,
    @InjectRepository(PointConfigurationEntity) private pointConfigRepo: Repository<PointConfigurationEntity>,
    @InjectRepository(NotificationEntity) private notificationRepo: Repository<NotificationEntity>,
  ) {}

  async getAll() {
    return this.submissionsRepo.find({ order: { id: 'DESC' } });
  }

  async createSubmission(studentId: number, payload: { title: string; description: string; evidence?: string; submissionType?: string }) {
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

    // Create notification for the student
    await this.notificationRepo.save(
      this.notificationRepo.create({
        userId: studentId,
        title: 'Submission Created',
        message: `Your submission "${payload.title}" has been sent for review.`,
      }),
    );

    // Create notifications for all admin users
    const admins = await this.usersRepo.find({ where: { role: 'admin' } });
    for (const admin of admins) {
      await this.notificationRepo.save(
        this.notificationRepo.create({
          userId: admin.id,
          title: 'New Submission',
          message: `A new submission "${payload.title}" is pending your review.`,
          link: '/admin/reviews',
        }),
      );
    }

    return saved;
  }

  async reviewSubmission(id: number, decision: 'approved' | 'rejected') {
    const submission = await this.submissionsRepo.findOne({ where: { id } });
    if (!submission) return null;

    if (decision === 'approved') {
      submission.status = 'approved';

      // Look up points from configuration
      const pointConfig = await this.pointConfigRepo.findOne({ where: { type: submission.submissionType } });
      submission.pointsAwarded = pointConfig ? pointConfig.points : 50;

      await this.submissionsRepo.save(submission);

      // Update UserEntity points
      const studentUser = await this.usersRepo.findOne({ where: { id: submission.studentId } });
      if (studentUser) {
        studentUser.points = (studentUser.points || 0) + submission.pointsAwarded;
        await this.usersRepo.save(studentUser);
      }

      // Update StudentEntity points
      const studentProfile = await this.studentRepo.findOne({ where: { id: submission.studentId } });
      if (studentProfile) {
        studentProfile.points = (studentProfile.points || 0) + submission.pointsAwarded;
        await this.studentRepo.save(studentProfile);
      }
    } else {
      submission.status = 'rejected';
      submission.pointsAwarded = 0;
      await this.submissionsRepo.save(submission);
    }

    return submission;
  }
}
